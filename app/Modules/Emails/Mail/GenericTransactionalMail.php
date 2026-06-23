<?php

namespace App\Modules\Emails\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericTransactionalMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $mailSubject, public string $bodyHtml, public ?string $bodyText = null) {}

    public function build(): self
    {
        $mail = $this->subject($this->mailSubject)->view('emails.generic-transactional', ['bodyHtml' => $this->bodyHtml]);

        if ($this->bodyText) {
            $mail->text('emails.generic-transactional-text', ['bodyText' => $this->bodyText]);
        }

        return $mail;
    }
}
