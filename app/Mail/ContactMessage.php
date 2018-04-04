<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

// the class name is the default email subject
class ContactMessage extends Mailable
{
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mail.ContactMessage');
    }
}
