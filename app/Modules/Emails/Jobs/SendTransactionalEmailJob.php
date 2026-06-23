<?php

namespace App\Modules\Emails\Jobs;

use App\Modules\Emails\Enums\EmailLogStatus;
use App\Modules\Emails\Mail\GenericTransactionalMail;
use App\Modules\Emails\Models\EmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendTransactionalEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $emailLogId, public string $bodyHtml, public ?string $bodyText = null) {}

    public function handle(): void
    {
        $log = EmailLog::query()->findOrFail($this->emailLogId);

        try {
            Mail::to($log->recipient_email, $log->recipient_name)->send(new GenericTransactionalMail($log->subject, $this->bodyHtml, $this->bodyText));
            $log->forceFill(['status' => EmailLogStatus::Sent, 'sent_at' => now(), 'error_message' => null])->save();
        } catch (Throwable $exception) {
            $log->forceFill(['status' => EmailLogStatus::Failed, 'failed_at' => now(), 'error_message' => $exception->getMessage()])->save();
        }
    }
}
