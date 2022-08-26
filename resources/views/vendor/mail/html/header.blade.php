<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="https://orders.gaelanmedical.com/images/logo-primary.png" class="logo" alt="Gaelan Medical Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
