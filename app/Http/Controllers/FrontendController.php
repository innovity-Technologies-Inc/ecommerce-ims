<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\ContactMessageRequest;
use App\Http\Requests\Client\ProductFilterRequest;
use App\Http\Requests\Client\TrackOrderRequest;
use App\Models\Faq;
use App\Models\FlashSale;
use App\Models\PolicySetting;
use App\Models\Product;
use App\Models\SectionSetting;
use App\Services\ContactService;
use App\Services\FrontendService;
use App\Services\HomepageService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FrontendController extends Controller
{
    public function __construct(
        protected HomepageService $homepageService,
        protected OrderService $orderService,
        protected ContactService $contactService,
        protected FrontendService $frontendService,
        protected \App\Services\FlashSaleService $flashSaleService,
        protected \App\Services\FaqService $faqService
    ) {}

    /**
     * Display the privacy policy page.
     */
    public function privacyPolicy(): View
    {
        $policy = PolicySetting::first();

        return view('client.pages.privacy_policy', [
            'policy' => $policy,
            'title' => 'Privacy Policy',
            'section' => 'Privacy Policy',
        ]);
    }

    /**
     * Display the return policy page.
     */
    public function returnPolicy(): View
    {
        $policy = PolicySetting::first();

        return view('client.pages.return_policy', [
            'policy' => $policy,
            'title' => 'Return Policy',
            'section' => 'Return Policy',
        ]);
    }

    /**
     * Display the FAQ page.
     */
    public function faq(): View
    {
        $faqs = $this->faqService->getActiveFaqs();

        return view('client.pages.faq', [
            'faqs' => $faqs,
            'title' => 'Frequently Asked Questions',
            'section' => 'FAQ',
        ]);
    }

    /**
     * Display the order tracking page.
     */
    public function trackOrder(TrackOrderRequest $request): View
    {
        $title = 'Track Your Order';
        $section = 'Track Order';
        $order = null;

        if ($request->filled('order_id')) {
            $order = $this->orderService->trackOrderById($request->order_id);
        }

        return view('client.track-order', compact('title', 'section', 'order'));
    }

    /**
     * Display the homepage.
     */
    public function home(): View
    {
        $sliders = $this->homepageService->getActiveSliders();

        $flashSale = $this->flashSaleService->getActiveFlashSale();

        $bestsellerSection = SectionSetting::where('section_name', 'bestsellers')->first();
        $bestsellingProducts = $this->homepageService->getSectionProducts('bestsellers');

        $hotDealsSection = SectionSetting::where('section_name', 'hot_deals')->first();
        $hotDealProducts = $this->homepageService->getSectionProducts('hot_deals');

        $featuredSection = SectionSetting::where('section_name', 'featured')->first();
        $featuredProducts = $this->homepageService->getSectionProducts('featured');

        $recentlyAddedSection = SectionSetting::where('section_name', 'recently_added')->first();
        $recentlyAddedProducts = $this->homepageService->getSectionProducts('recently_added');

        $topPicksSection = SectionSetting::where('section_name', 'top_picks')->first();
        $topPicksProducts = $this->homepageService->getSectionProducts('top_picks');

        return view('client.homepage', compact(
            'sliders',
            'flashSale',
            'bestsellerSection',
            'bestsellingProducts',
            'hotDealsSection',
            'hotDealProducts',
            'featuredSection',
            'featuredProducts',
            'recentlyAddedSection',
            'recentlyAddedProducts',
            'topPicksSection',
            'topPicksProducts'
        ));
    }

    /**
     * Display the products listing page with filters.
     */
    public function products(ProductFilterRequest $request): View
    {
        $products = $this->frontendService->getFilteredProducts($request->validated());
        $flashSales = FlashSale::where('status', true)->get();

        return view('client.products', [
            'products' => $products,
            'flashSales' => $flashSales,
            'title' => 'Shop Products',
            'section' => 'Products',
        ]);
    }

    /**
     * Display the product details page.
     */
    public function productDetails(string $slug): View
    {
        $product = Product::where('slug', $slug)
            ->with(['primaryImage', 'images', 'category', 'brand', 'variants'])
            ->firstOrFail();

        $relatedProducts = $this->frontendService->getRelatedProducts($product);

        return view('client.product_details', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'title' => $product->name,
            'section' => 'Product Details',
        ]);
    }

    /**
     * Display the contact us page.
     */
    public function contact(): View
    {
        return view('client.contact', [
            'title' => 'Contact Us',
            'section' => 'Contact Us',
        ]);
    }

    /**
     * Store a new contact message.
     */
    public function storeContactMessage(ContactMessageRequest $request): RedirectResponse
    {
        $this->contactService->storeMessage($request->validated());

        return back()->with([
            'message' => 'Thanks for contacting us. We will be reaching out soon.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Display the invoice for printing (Public Access).
     */
    public function publicInvoice(string $orderId): View
    {
        $order = $this->orderService->trackOrderById($orderId);

        if (! $order) {
            abort(404);
        }

        // Auto-generate invoice if not exists
        if (! $order->invoice_no) {
            $this->orderService->generateInvoice($order);
        }

        $order->load(['orderItems']);

        return view('client.orders.invoice-print', compact('order'));
    }
}
