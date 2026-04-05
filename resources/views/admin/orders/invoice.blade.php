<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; color: #333; margin: 0; padding: 20px; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; background: #fff; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; table-layout: fixed; }
        .invoice-box table td { padding: 8px; vertical-align: top; }
        
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        
        .invoice-box table tr.heading td { background: #f8f9fa; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.total td:last-child { border-top: 2px solid #333; font-weight: bold; }
        
        /* Column Widths */
        .col-name { width: 45%; }
        .col-qty { width: 15%; text-align: center !important; }
        .col-price { width: 20%; text-align: right !important; }
        .col-total { width: 20%; text-align: right !important; }
        
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .fw-bold { font-weight: bold; }
        .text-muted { color: #777; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .invoice-box { box-shadow: none; border: none; max-width: 100%; padding: 10px; }
        }
        
        .btn-group { margin-bottom: 20px; text-align: right; }
        .btn { padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; font-size: 14px; }
        .btn-primary { background: #7AAACE; color: #fff; }
        .btn-dark { background: #333; color: #fff; }
    </style>
</head>
<body>
    <div class="btn-group no-print">
        <a href="javascript:window.print()" class="btn btn-primary">Print / Download PDF</a>
        <a href="{{ url()->previous() }}" class="btn btn-dark">Back</a>
    </div>

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="4" style="padding: 0;">
                    <table style="table-layout: auto;">
                        <tr>
                            <td class="title" style="padding-left: 0;">
                                @php $gs = \App\HelperClass::generalSettings(); @endphp
                                @if($gs && $gs->dark_logo)
                                    <img src="{{ asset('storage/'.$gs->dark_logo) }}" style="width:100%; max-width:180px;">
                                @else
                                    <h2 style="margin:0;">{{ config('app.name') }}</h2>
                                @endif
                            </td>
                            <td class="text-right" style="padding-right: 0;">
                                <h4 style="margin:0; color: #333;">INVOICE</h4>
                                <strong>#{{ $order->invoice_no }}</strong><br>
                                Date: {{ $order->invoice_date->format('M d, Y') }}<br>
                                Order: {{ $order->order_id }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="4" style="padding: 0; padding-top: 20px;">
                    <table style="table-layout: auto;">
                        <tr>
                            <td style="width: 50%; padding-left: 0;">
                                @php
                                    $cs = \App\HelperClass::contactSettings();
                                @endphp
                                <h6 style="margin:0 0 5px 0; color: #777; text-transform: uppercase; font-size: 12px;">Seller</h6>
                                <strong>{{ $cs->company_name ?? $gs->business_name ?? config('app.name') }}</strong><br>
                                {!! nl2br(e($cs->address ?? '')) !!}<br>
                                @if($cs && $cs->phone_number) Phone: {{ $cs->phone_number }}<br> @endif
                                @if($cs && $cs->company_email) Email: {{ $cs->company_email }} @endif
                            </td>
                            <td style="width: 50%; padding-right: 0;" class="text-right">
                                <h6 style="margin:0 0 5px 0; color: #777; text-transform: uppercase; font-size: 12px;">Buyer & Shipping</h6>
                                <strong>{{ $order->name }}</strong><br>
                                {{ $order->mobile }}<br>
                                {{ $order->address }}<br>
                                {{ $order->city }}, {{ $order->state }} {{ $order->zip }}<br>
                                {{ $order->country }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td colspan="2">Payment Method</td>
                <td colspan="2" class="text-right">Status</td>
            </tr>
            <tr class="details">
                <td colspan="2" style="padding-bottom: 30px;">{{ $order->payment_method }}</td>
                <td colspan="2" class="text-right" style="padding-bottom: 30px;">{{ $order->payment_status }}</td>
            </tr>

            <tr class="heading">
                <td class="col-name">Item</td>
                <td class="col-qty">Qty</td>
                <td class="col-price">Price</td>
                <td class="col-total">Subtotal</td>
            </tr>
            @foreach($order->orderItems as $item)
                <tr class="item">
                    <td>
                        <span class="fw-bold">{{ $item->product_name }}</span>
                        @if($item->variant_name)
                            <br><small class="text-muted">Variant: {{ $item->variant_name }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">${{ number_format($item->regular_price, 2) }}</td>
                    <td class="text-right">${{ number_format($item->regular_price * $item->quantity, 2) }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="2"></td>
                <td class="fw-bold text-right" style="border-top: 2px solid #eee;">Gross Subtotal</td>
                <td class="text-right" style="border-top: 2px solid #eee;">${{ number_format($order->orderItems->sum(fn($i) => $i->regular_price * $i->quantity), 2) }}</td>
            </tr>
            @if($order->product_discount > 0)
            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right text-muted">Product Discount</td>
                <td class="text-right text-danger">-${{ number_format($order->product_discount, 2) }}</td>
            </tr>
            @endif
            @if($order->discount > 0)
            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right text-muted">Coupon Discount</td>
                <td class="text-right text-danger">-${{ number_format($order->discount, 2) }}</td>
            </tr>
            @endif
            <tr class="total">
                <td colspan="2"></td>
                <td class="text-right text-muted">Shipping</td>
                <td class="text-right">${{ number_format($order->shipping_charge, 2) }}</td>
            </tr>
            <tr class="total">
                <td colspan="2"></td>
                <td class="fw-bold text-right" style="font-size: 18px;">Grand Total</td>
                <td class="fw-bold text-right" style="font-size: 18px; color: #333;">${{ number_format($order->total_amount, 2) }}</td>
            </tr>
        </table>
        <div style="margin-top: 50px; text-align: center; color: #777; font-size: 12px;">
            Thank you for your business!<br>
            If you have any questions about this invoice, please contact us.
        </div>
    </div>
    
    <script>
        // Auto trigger print if requested via URL parameter
        if (window.location.search.indexOf('print=1') > -1) {
            window.onload = function() { window.print(); }
        }
    </script>
</body>
</html>
