<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class InvitationMail extends Mailable
{
    public $url;

    public function __construct(
        $url
    )
    {
        $this->url = $url;
    }

    public function build()
    {
        return $this
            ->subject(
                'Invitation to Join Plainthing'
            )
            ->view(
                'emails.invitation'
            );
    }
}