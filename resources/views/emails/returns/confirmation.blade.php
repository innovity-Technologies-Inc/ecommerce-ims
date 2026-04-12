<x-mail::message>
# Return Request Received

Hello,

We have received your return request for Order **#{{ $returnRequest->order->order_id }}**.

**Return ID:** {{ $returnRequest->return_id }}
**Reason:** {{ $returnRequest->reason }}

**Items requested for return:**
<x-mail::table>
| Product | Variant | Qty | Price |
| :--- | :--- | :---: | :---: |
@foreach($returnRequest->returnItems as $item)
| {{ $item->product->name }} | {{ $item->productVariant->variant_name ?? 'N/A' }} | {{ $item->quantity }} | ${{ number_format($item->total_price, 2) }} |
@endforeach
</x-mail::table>

Our team will review your request and get back to you shortly.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
