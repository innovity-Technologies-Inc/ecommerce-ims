<x-mail::message>
# Supplier RMA Request: {{ $rma->rma_number }}

Dear {{ $rma->supplier->name }},

We are initiating a return request for the following items due to damage/defects.

**RMA Number:** {{ $rma->rma_number }}  
**Date:** {{ $rma->created_at->format('M d, Y') }}  
@if($rma->purchaseOrder)
**Related PO:** {{ $rma->purchaseOrder->po_number }}
@endif

<x-mail::table>
| Product | Batch | Quantity | Serial Numbers |
| :--- | :--- | :---: | :--- |
@foreach($rma->rmaItems->groupBy(fn($item) => $item->batch_id . '-' . $item->product_id . '-' . ($item->product_variant_id ?? '')) as $group)
@php 
    $first = $group->first();
    $serials = $group->pluck('serial.serial_no')->filter()->implode(', ');
@endphp
| {{ $first->product->name }} {{ $first->variant ? '(' . $first->variant->variant_name . ')' : '' }} | {{ $first->batch->batch_number }} | {{ $group->sum('quantity') }} | {{ $serials ?: 'N/A' }} |
@endforeach
</x-mail::table>

**Remarks:**  
{{ $rma->remarks ?? 'No additional remarks.' }}

Please let us know the next steps for this return.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
