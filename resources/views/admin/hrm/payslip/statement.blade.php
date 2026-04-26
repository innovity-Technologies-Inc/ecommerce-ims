<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Statement - {{ $payslip->payslip_number }}</title>
    <link rel="stylesheet" href="{{ asset('admin_assets/assets/css/bootstrap.min.css') }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
        
        body { background-color: #f0f2f5; font-family: 'Inter', sans-serif; -webkit-print-color-adjust: exact; }
        .statement-container { max-width: 900px; margin: 30px auto; }
        
        .payslip-box { 
            background: #fff; 
            padding: 50px; 
            border: 1px solid #ddd; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            position: relative;
        }

        .header-section { border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
        .company-name { font-size: 28px; font-weight: 800; color: #10b981; letter-spacing: -0.5px; }
        .statement-label { background: #10b981; color: #fff; padding: 5px 15px; font-weight: 700; text-transform: uppercase; font-size: 14px; border-radius: 4px; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .info-item { margin-bottom: 15px; }
        .info-item label { display: block; color: #6b7280; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 3px; }
        .info-item span { color: #111827; font-size: 15px; font-weight: 600; }

        .earnings-table { width: 100%; margin-bottom: 40px; border: 1px solid #e5e7eb; }
        .earnings-table th { background: #f9fafb; padding: 12px 20px; text-align: left; font-size: 12px; font-weight: 700; color: #374151; border-bottom: 1px solid #e5e7eb; }
        .earnings-table td { padding: 15px 20px; font-size: 14px; color: #1f2937; border-bottom: 1px solid #e5e7eb; }
        
        .total-box { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; }
        .total-label { font-size: 16px; font-weight: 700; color: #166534; }
        .total-amount { font-size: 24px; font-weight: 800; color: #15803d; }

        .signature-section { margin-top: 120px; display: flex; justify-content: space-between; padding: 0 20px; }
        .signature-box { width: 220px; text-align: center; }
        .signature-line { border-top: 2px solid #333; margin-bottom: 8px; }
        .signature-text { font-size: 13px; font-weight: 700; color: #111; }

        .footer-note { margin-top: 80px; text-align: center; color: #6b7280; font-size: 11px; border-top: 1px dashed #e5e7eb; padding-top: 20px; }

        @media print {
            @page { size: portrait; margin: 1cm; }
            body { background: #fff !important; margin: 0; padding: 0; display: block !important; }
            .statement-container { margin: 0; max-width: 100% !important; padding: 0 !important; }
            .payslip-box { box-shadow: none !important; border: 1px solid #eee !important; padding: 40px !important; margin: 0 !important; }
            .no-print { display: none !important; }
            .total-box { -webkit-print-color-adjust: exact; background-color: #f0fdf4 !important; border: 1px solid #bbf7d0 !important; }
            .header-section { border-bottom: 2px solid #10b981 !important; }
        }
    </style>
</head>
<body>

@php $gs = \App\HelperClass::generalSettings(); @endphp

<div class="statement-container">
    <div class="no-print mb-4 text-center">
        <button onclick="window.print()" class="btn btn-primary px-4 py-2 fw-bold">Print Statement</button>
        <button onclick="window.close()" class="btn btn-outline-secondary px-4 py-2 ms-2">Close Window</button>
    </div>

    <div class="payslip-box">
        <div class="header-section d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                @if($gs->light_logo)
                    <img src="{{ asset('storage/' . $gs->light_logo) }}" alt="logo" style="max-height: 50px;">
                @endif
                <div>
                    <div class="company-name text-uppercase">{{ $gs->business_name ?? 'Smart Ecom' }}</div>
                </div>
            </div>
            <div class="text-end">
                <div class="statement-label mb-2">Salary Statement</div>
                <div class="fw-bold text-dark">#{{ $payslip->payslip_number }}</div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-group">
                <div class="info-item">
                    <label>Employee Name</label>
                    <span>{{ $payslip->admin->name }}</span>
                </div>
                <div class="info-item">
                    <label>Email Address</label>
                    <span>{{ $payslip->admin->email }}</span>
                </div>
                <div class="info-item">
                    <label>Payment Method</label>
                    <span>Bank Transfer / Cash</span>
                </div>
            </div>
            <div class="info-group" style="text-align: right;">
                <div class="info-item">
                    <label>Pay Period</label>
                    <span>{{ $payslip->start_date->format('d M, Y') }} - {{ $payslip->end_date->format('d M, Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Statement Date</label>
                    <span>{{ $payslip->created_at->format('d M, Y') }}</span>
                </div>
                <div class="info-item">
                    <label>Status</label>
                    <span class="text-success text-uppercase">{{ $payslip->status }}</span>
                </div>
            </div>
        </div>

        <table class="earnings-table">
            <thead>
                <tr>
                    <th>DESCRIPTION</th>
                    <th class="text-center">HOURS</th>
                    <th class="text-end">UNIT RATE</th>
                    <th class="text-end">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Work Pay (Based on recorded attendance)</td>
                    <td class="text-center">{{ number_format($payslip->total_hours, 2) }} hrs</td>
                    <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->salary_amount, 2) }}</td>
                    <td class="text-end">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-label text-uppercase">Net Salary Payout</div>
            <div class="total-amount">{{ \App\HelperClass::generalSettings()->currency ?? '$' }}{{ number_format($payslip->net_salary, 2) }}</div>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-text">Employee Signature</div>
                <div class="text-muted small" style="font-size: 10px;">Date: ____/____/20____</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-text">Authorized Signature</div>
                <div class="text-muted small" style="font-size: 10px;">{{ \App\HelperClass::generalSettings()->business_name }}</div>
            </div>
        </div>

        <div class="footer-note">
            This is a system-generated salary advice. For any discrepancies, please contact the HR department within 48 hours.
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        if (!new URLSearchParams(window.location.search).has('no_auto_print')) {
            // Set clean title for print header
            document.title = "Salary Statement";
            
            setTimeout(function() {
                window.print();
            }, 500);
        }
    }
</script>
</body>
</html>
