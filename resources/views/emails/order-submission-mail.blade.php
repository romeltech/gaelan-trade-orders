@component('mail::message')
{{-- # Hi {{ $adminAccounts[0]['profile']['full_name'] }}, --}}
# Hi Admin,

A new order has been submitted from the Application.

# Order Number: {{$data['order_number']}}

@component('mail::button', ['url' => url('d/orders/submitted')])
Submitted Orders
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
