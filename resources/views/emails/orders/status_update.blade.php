@php $gs = \App\HelperClass::generalSettings(); @endphp
<x-mail::message>
# Order Status Update

Dear {{ $order->name }},

The status of your order **{{ $order->order_id }}** has been updated.

**Current Status:** {{ $order->order_status }}

@if($order->rejection_reason)
**Reason/Remarks:** {{ $order->rejection_reason }}
@endif

**Order Details:**
**Order ID:** {{ $order->order_id }}
**Order Date:** {{ $order->created_at->format('M d, Y') }}
**Total Amount:** {{ $gs->currency ?? '$' }}{{ number_format($order->total_amount, 2) }}

You can view your order details in your account.

If you have any questions, please reply to this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
