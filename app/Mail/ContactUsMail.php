<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userEmail;
    public $userMessage;

    /**
     * Create a new message instance.
     */
    public function __construct($userEmail, $userMessage)
    {
        $this->userEmail = $userEmail;
        $this->userMessage = $userMessage;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Contact Us Message')
                    ->view('emails.contact_us') // blade file ka path
                    ->with([
                        'userEmail' => $this->userEmail,
                        'userMessage' => $this->userMessage,
                    ]);
    }
}
