@php $gs = \App\HelperClass::generalSettings(); @endphp
<x-mail::message>
# Return Request Updated

Hello,

The status of your return request **#{{ $returnRequest->return_id }}** for Order **#{{ $returnRequest->order->order_id }}** has been updated.

**Current Status:** {{ ucfirst($returnRequest->status) }}

@if($returnRequest->status === 'rejected')
**Rejection Reason:** {{ $returnRequest->rejection_reason }}
@endif

@if($returnRequest->status === 'approved')
Our team has approved your return request. Please proceed with shipping the items back to us as per our return policy. Once we receive and verify the items, your refund/stock restoration will be processed.
@endif

**Return Items Details:**
<x-mail::table>
| Product | Variant | Qty | Price |
| :--- | :--- | :---: | :---: |
@foreach($returnRequest->returnItems as $item)
| {{ $item->product->name }} | {{ $item->productVariant->variant_name ?? 'N/A' }} | {{ $item->quantity }} | {{ $gs->currency ?? '$' }}{{ number_format($item->total_price, 2) }} |
@endforeach
</x-mail::table>

If you have any questions, feel free to contact our support team.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
