@props(['url'])
@php
    $gs = \App\HelperClass::generalSettings();
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($gs && $gs->dark_logo)
<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($gs->dark_logo) }}" class="logo" alt="{{ $gs->business_name ?? 'Logo' }}" style="max-height: 50px; width: auto;">
@elseif (trim($slot) === 'Laravel')
<img src="https://laravel.com/img/notification-logo-v2.1.png" class="logo" alt="Laravel Logo">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
