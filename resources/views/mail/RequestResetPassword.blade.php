@component('mail::message')

# Reset password

Hello {{ $user->name }},

To reset your password, click following link and type in your new password:

@component('mail::button', ['url' => $resetPasswordUrl])
Reset password
@endcomponent

Thanks,<br>
{{ env('APP_NAME') }}<br>
{{ env('APP_DOMAIN') }}

@endcomponent
