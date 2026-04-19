<x-mail::message>
# Purchase Order: {{ $po->po_number }}

Dear {{ $po->supplier->name }},

Please find the details of our purchase order below.

**PO Number:** {{ $po->po_number }}  
**Order Date:** {{ $po->order_date->format('M d, Y') }}  
**Expected Delivery:** {{ $po->expected_delivery_date ? $po->expected_delivery_date->format('M d, Y') : 'N/A' }}

<x-mail::table>
@php $gs = \App\HelperClass::generalSettings(); @endphp
| Product | Quantity | Unit Cost | Subtotal |
| :--- | :---: | :---: | :---: |
@foreach($po->items as $item)
| {{ $item->product->name }} {{ $item->variant ? '(' . $item->variant->variant_name . ')' : '' }} | {{ $item->order_quantity }} | {{ $gs->currency ?? '$' }}{{ number_format($item->unit_cost, 2) }} | {{ $gs->currency ?? '$' }}{{ number_format($item->subtotal, 2) }} |
@endforeach
| **Total** | | | **{{ $gs->currency ?? '$' }}{{ number_format($po->total_amount, 2) }}** |
</x-mail::table>

**Notes:**  
{{ $po->notes ?? 'No additional notes.' }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
