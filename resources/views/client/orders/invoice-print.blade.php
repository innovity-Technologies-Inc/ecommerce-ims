<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        body { font-family: 'Arial', sans-serif; color: #333; margin: 0; padding: 20px; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; background: #fff; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .invoice-box { box-shadow: none; border: none; }
        }
        
        .btn-group { margin-bottom: 20px; text-align: right; }
        .btn { padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-block; font-size: 14px; }
        .btn-primary { background: #7AAACE; color: #fff; }
        .btn-dark { background: #333; color: #fff; }
    </style>
</head>
<body>
    <div class="btn-group no-print">
        <a href="javascript:window.print()" class="btn btn-primary">Download PDF / Print</a>
        <a href="{{ route('user.order_details', $order->order_id) }}" class="btn btn-dark">Back to Order</a>
    </div>

    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                @php $gs = \App\HelperClass::generalSettings(); @endphp
                                @if($gs && $gs->dark_logo)
                                    <img src="{{ asset('storage/'.$gs->dark_logo) }}" style="width:100%; max-width:200px;">
                                @else
                                    {{ config('app.name') }}
                                @endif
                            </td>
                            <td>
                                <strong>Invoice #: {{ $order->invoice_no }}</strong><br>
                                Created: {{ $order->invoice_date->format('M d, Y') }}<br>
                                Order ID: {{ $order->order_id }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                @php
                                    $gs = \App\HelperClass::generalSettings();
                                    $cs = \App\HelperClass::contactSettings();
                                @endphp
                                <strong>Seller:</strong><br>
                                {{ $cs->company_name ?? $gs->business_name ?? config('app.name') }}<br>
                                @if($cs && $cs->address)
                                    {{ $cs->address }}<br>
                                @endif
                                @if($cs && $cs->phone_number)
                                    Phone: {{ $cs->phone_number }}<br>
                                @endif
                                @if($cs && $cs->company_email)
                                    Email: {{ $cs->company_email }}
                                @endif
                            </td>
                            <td>
                                <strong>Buyer:</strong><br>
                                {{ $order->name }}<br>
                                {{ $order->email }}<br>
                                {{ $order->mobile }}
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <strong>Shipping Address:</strong><br>
                                {{ $order->address }}<br>
                                {{ $order->city }}, {{ $order->state }} {{ $order->zip }}<br>
                                {{ $order->country }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr class="heading">
                <td>Payment Method</td>
                <td>Status</td>
            </tr>
            <tr class="details">
                <td>{{ $order->payment_method }}</td>
                <td>{{ $order->payment_status }}</td>
            </tr>

            <tr class="heading">
                <td>Item</td>
                <td>Price</td>
            </tr>
            @foreach($order->orderItems as $item)
                <tr class="item">
                    <td>
                        {{ $item->product_name }}
                        @if($item->variant_name)
                            <br><small style="color: #777;">Variant: {{ $item->variant_name }}</small>
                        @endif
                        <br><small style="color: #777;">Qty: {{ $item->quantity }}</small>
                    </td>
                    <td>${{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach

            <tr class="total">
                <td></td>
                <td>
                   Subtotal: ${{ number_format($order->subtotal, 2) }}<br>
                   Shipping: ${{ number_format($order->shipping_charge, 2) }}<br>
                   @if($order->discount > 0)
                       Discount: -${{ number_format($order->discount, 2) }}<br>
                   @endif
                   <hr>
                   <strong>Grand Total: ${{ number_format($order->total_amount, 2) }}</strong>
                </td>
            </tr>
        </table>
        <div style="margin-top: 50px; text-align: center; color: #777; font-size: 12px;">
            Thank you for shopping with {{ config('app.name') }}!<br>
            For any queries, please visit our help center.
        </div>
    </div>
    
    <script>
        // Auto trigger print
        window.onload = function() { window.print(); }
    </script>
</body>
</html>
