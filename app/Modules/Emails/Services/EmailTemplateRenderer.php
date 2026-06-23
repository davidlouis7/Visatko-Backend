<?php

namespace App\Modules\Emails\Services;

use App\Modules\Emails\Models\EmailTemplate;

class EmailTemplateRenderer
{
    /** @param array<string, mixed> $variables */
    public function render(EmailTemplate $template, array $variables): array
    {
        $replace = collect($variables)->mapWithKeys(fn ($value, $key): array => ['{{'.$key.'}}' => (string) $value])->all();

        return [
            'subject' => strtr($template->subject, $replace),
            'body_html' => strtr($template->body_html, $replace),
            'body_text' => $template->body_text ? strtr($template->body_text, $replace) : null,
        ];
    }
}
