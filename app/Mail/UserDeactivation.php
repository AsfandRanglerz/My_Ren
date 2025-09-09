<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;   // 👈 ye line add karo
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserDeactivation extends Mailable implements ShouldQueue   // 👈 queue enable
{
    use Queueable, SerializesModels;

    public $user;
    public $reason;

    public function __construct($user, $reason)
    {
        $this->user = $user;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->subject('Account Deactivation Notification')
                    ->view('emails.user_deactivation')
                    ->with([
                        'user' => $this->user,
                        'reason' => $this->reason,
                    ]);
    }
}
