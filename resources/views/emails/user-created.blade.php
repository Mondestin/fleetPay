@component('mail::message')
# Welcome to FleetPay

Hi {{ $user->first_name }},

Your account has been created successfully.
Your temporary password is: "password"

Please change your password after logging in.

Thanks,<br>
{{ config('app.name') }}
@endcomponent 