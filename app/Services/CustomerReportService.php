<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Support\Facades\DB;

class CustomerReportService
{
    /**
     * Get Overview Statistics for Customer Reports
     */
    public function getOverviewStats(array $filters): array
    {
        $startDate = $filters['start_date'] ?? Carbon::now()->startOfMonth();
        $endDate = $filters['end_date'] ?? Carbon::now()->endOfDay();

        $totalCustomers = User::count();
        $newCustomers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        $returningCustomers = User::whereHas('orders', function ($query) use ($startDate, $endDate) {
            $query->where('order_status', '!=', 'cancelled')
                ->whereBetween('created_at', [$startDate, $endDate]);
        }, '>', 1)->count();

        $activeCustomers = User::whereHas('orders', function ($query) {
            $query->where('created_at', '>=', Carbon::now()->subMonths(3));
        })->count();

        $avgOrderValue = Order::where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->avg('total_amount') ?? 0;

        return [
            'total_customers' => $totalCustomers,
            'new_customers' => $newCustomers,
            'returning_customers' => $returningCustomers,
            'active_customers' => $activeCustomers,
            'avg_order_value' => $avgOrderValue,
        ];
    }

    /**
     * Get Filtered Customer List with Aggregate Data using FlexSearch
     */
    public function getCustomerList(array $params)
    {
        $query = User::query()
            ->withCount(['orders' => function ($q) {
                $q->where('order_status', '!=', 'cancelled');
            }])
            ->withSum(['orders' => function ($q) {
                $q->where('order_status', '!=', 'cancelled');
            }], 'total_amount')
            ->addSelect([
                'last_order_date' => Order::select('created_at')
                    ->whereColumn('user_id', 'users.id')
                    ->latest()
                    ->limit(1),
            ]);

        // Explicitly define which fields can be used as direct WHERE filters
        $filters = [];
        if (isset($params['status']) && $params['status'] !== '') {
            $filters['status'] = $params['status'];
        }

        $searchableColumns = ['name', 'email', 'mobile', 'city', 'state'];
        $searchTerm = $params['search'] ?? null;

        $flexSearch = app(FlexSearch::class);

        // Apply Search and Filtering using FlexSearch
        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        return $query
            ->orderBy($params['sort_by'] ?? 'orders_sum_total_amount', $params['sort_order'] ?? 'desc')
            ->paginate($params['per_page'] ?? 10);
    }

    /**
     * Perform RFM Analysis (Recency, Frequency, Monetary)
     */
    public function getRFMAnalysis(): array
    {
        $customers = User::whereHas('orders', function ($q) {
            $q->where('order_status', '!=', 'cancelled');
        })->get();

        $rfmStats = $customers->map(function ($user) {
            $lastOrder = Order::where('user_id', $user->id)
                ->where('order_status', '!=', 'cancelled')
                ->latest()
                ->first();

            $orderCount = Order::where('user_id', $user->id)
                ->where('order_status', '!=', 'cancelled')
                ->count();

            $totalSpent = Order::where('user_id', $user->id)
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount');

            $recency = $lastOrder ? Carbon::parse($lastOrder->created_at)->diffInDays(Carbon::now()) : 999;

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'recency' => $recency,
                'frequency' => $orderCount,
                'monetary' => $totalSpent,
            ];
        });

        // Simple segmentation logic (Can be refined with percentiles)
        $segments = [
            'VIP' => $rfmStats->filter(fn ($c) => $c['recency'] <= 30 && $c['frequency'] >= 5 && $c['monetary'] >= 1000),
            'Loyal' => $rfmStats->filter(fn ($c) => $c['recency'] <= 60 && $c['frequency'] >= 3),
            'At Risk' => $rfmStats->filter(fn ($c) => $c['recency'] > 90 && $c['recency'] <= 180),
            'Lost' => $rfmStats->filter(fn ($c) => $c['recency'] > 180),
            'Others' => $rfmStats->filter(fn ($c) => $c['recency'] <= 90 && $c['frequency'] < 3),
        ];

        return [
            'stats' => $rfmStats,
            'segments' => $segments,
        ];
    }

    /**
     * Get Purchase Behavior Analytics
     */
    public function getPurchaseBehavior(array $filters): array
    {
        $startDate = $filters['start_date'] ?? Carbon::now()->startOfYear();
        $endDate = $filters['end_date'] ?? Carbon::now()->endOfDay();

        // Orders by Status
        $orderStatusDistribution = Order::select('order_status', DB::raw('count(*) as count'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('order_status')
            ->get();

        // Average Order Value Trend
        $aovTrend = Order::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('AVG(total_amount) as aov')
        )
            ->where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'status_distribution' => $orderStatusDistribution,
            'aov_trend' => $aovTrend,
        ];
    }

    /**
     * Get Cohort Analysis (Retention Heatmap)
     */
    public function getCohortAnalysis(): array
    {
        // Get users grouped by their signup month (Cohort)
        $cohorts = User::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as cohort_month"),
            DB::raw('count(*) as total_users')
        )
            ->groupBy('cohort_month')
            ->orderBy('cohort_month', 'desc')
            ->limit(12)
            ->get();

        $data = [];

        foreach ($cohorts as $cohort) {
            $monthData = ['cohort' => $cohort->cohort_month, 'total' => $cohort->total_users, 'retention' => []];

            for ($i = 0; $i < 6; $i++) {
                $activityMonth = Carbon::parse($cohort->cohort_month.'-01')->addMonths($i);

                if ($activityMonth->isAfter(Carbon::now())) {
                    break;
                }

                $activeUsers = User::where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $cohort->cohort_month)
                    ->whereHas('orders', function ($q) use ($activityMonth) {
                        $q->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m')"), $activityMonth->format('Y-m'))
                            ->where('order_status', '!=', 'cancelled');
                    })->count();

                $monthData['retention'][$i] = [
                    'count' => $activeUsers,
                    'percentage' => $cohort->total_users > 0 ? round(($activeUsers / $cohort->total_users) * 100, 1) : 0,
                ];
            }

            $data[] = $monthData;
        }

        return $data;
    }

    /**
     * Get Customer Lifetime Value (CLV) Projections
     */
    public function getCLVProjections(array $filters): array
    {
        $customers = User::whereHas('orders', function ($q) {
            $q->where('order_status', '!=', 'cancelled');
        })->get();

        $lifespanMonths = 24; // Standard business assumption for projection

        $clvData = $customers->map(function ($user) use ($lifespanMonths) {
            $totalSpent = Order::where('user_id', $user->id)
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount');

            $orderCount = Order::where('user_id', $user->id)
                ->where('order_status', '!=', 'cancelled')
                ->count();

            $firstOrder = Order::where('user_id', $user->id)
                ->where('order_status', '!=', 'cancelled')
                ->oldest()
                ->first();

            $monthsActive = $firstOrder ? max(1, Carbon::parse($firstOrder->created_at)->diffInMonths(Carbon::now())) : 1;

            $aov = $orderCount > 0 ? ($totalSpent / $orderCount) : 0;
            $frequencyPerMonth = $orderCount / $monthsActive;

            // Predictive CLV Formula: (AOV * Monthly Frequency * Lifespan)
            $projectedFutureValue = $aov * $frequencyPerMonth * $lifespanMonths;
            $totalCLV = $totalSpent + $projectedFutureValue;

            return [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'historical_value' => $totalSpent,
                'projected_value' => $projectedFutureValue,
                'total_clv' => $totalCLV,
                'aov' => $aov,
                'frequency' => round($frequencyPerMonth, 2),
                'status' => $totalCLV > 2000 ? 'High Value (Whale)' : ($totalCLV > 500 ? 'Medium Value' : 'Standard'),
            ];
        });

        return [
            'top_customers' => $clvData->sortByDesc('total_clv')->take(20),
            'averages' => [
                'avg_clv' => $clvData->avg('total_clv'),
                'avg_historical' => $clvData->avg('historical_value'),
            ],
            'segments' => [
                'whales' => $clvData->filter(fn ($c) => $c['total_clv'] > 2000)->count(),
                'medium' => $clvData->filter(fn ($c) => $c['total_clv'] > 500 && $c['total_clv'] <= 2000)->count(),
                'standard' => $clvData->filter(fn ($c) => $c['total_clv'] <= 500)->count(),
            ],
        ];
    }
}
