@component('mail::message')
{{-- # Hi {{ $adminAccounts[0]['profile']['full_name'] }}, --}}
# Hi Admin,

A new order has been submitted from the Application.
Order Number: {{$data['order_number']}}
<br />
<br />
<br />
@component('mail::button', ['url' => url('d/orders/submitted')])
Submitted Orders
@endcomponent
<br />
<br />
Thanks,<br>
{{ config('app.name') }}
@endcomponent
