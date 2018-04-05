<?php

namespace App\Mail;

use App\Models\UserModel;
use Illuminate\Mail\Mailable;

// the class name is the default email subject
class RegisterConfirmation extends Mailable
{
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(UserModel $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown(
            'mail.RegisterConfirmation',
            ['verifyEmailUrl' => env('APP_DOMAIN') . '/verify-email/' . $this->user->email_verified]
        );
    }
}
