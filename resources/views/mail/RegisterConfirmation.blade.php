@component('mail::message')

# Register Confirmation

Hello {{ $user->name }},

Your account has been created successfully.

Please click following link to verify your email address:

@component('mail::button', ['url' => $verifyEmailUrl])
Verify your email address
@endcomponent

Thanks,<br>
{{ env('APP_NAME') }}<br>
{{ env('APP_DOMAIN') }}

@endcomponent
