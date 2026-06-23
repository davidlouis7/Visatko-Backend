<?php

namespace App\Modules\Emails\Actions;

use App\Modules\Emails\Enums\EmailLogStatus;
use App\Modules\Emails\Jobs\SendTransactionalEmailJob;
use App\Modules\Emails\Models\EmailLog;
use App\Modules\Emails\Models\EmailTemplate;
use App\Modules\Emails\Services\EmailTemplateRenderer;
use App\Modules\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Model;

class SendTransactionalEmailAction
{
    public function __construct(private readonly EmailTemplateRenderer $renderer) {}

    /** @param array<string, mixed> $variables */
    public function execute(string $templateKey, ?string $recipientEmail, ?string $recipientName = null, array $variables = [], ?Model $related = null): ?EmailLog
    {
        if (! $recipientEmail || ! $this->setting('email_enabled', true)) {
            return null;
        }

        $template = EmailTemplate::query()->where('key', $templateKey)->where('is_active', true)->first();
        if (! $template) {
            return null;
        }

        $variables = array_merge(['frontend_url' => $this->setting('frontend_url', config('app.url'))], $variables);
        $rendered = $this->renderer->render($template, $variables);
        $log = EmailLog::query()->create([
            'template_key' => $templateKey,
            'subject' => $rendered['subject'],
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'status' => EmailLogStatus::Queued,
            'related_type' => $related?->getMorphClass(),
            'related_id' => $related?->getKey(),
            'payload' => $variables,
        ]);

        SendTransactionalEmailJob::dispatch($log->id, $rendered['body_html'], $rendered['body_text']);

        return $log;
    }

    private function setting(string $key, mixed $default = null): mixed
    {
        $setting = Setting::query()->where('group', 'email')->where('key', $key)->first();

        return $setting ? $setting->resolvedValue() : $default;
    }
}
