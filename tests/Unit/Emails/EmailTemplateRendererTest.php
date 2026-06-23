<?php

namespace Tests\Unit\Emails;

use App\Modules\Emails\Models\EmailTemplate;
use App\Modules\Emails\Services\EmailTemplateRenderer;
use Tests\TestCase;

class EmailTemplateRendererTest extends TestCase
{
    public function test_it_renders_simple_variables(): void
    {
        $template = new EmailTemplate(['subject' => 'Hello {{customer_name}}', 'body_html' => '<p>{{invoice_number}}</p>', 'body_text' => '{{amount_due}}']);
        $rendered = app(EmailTemplateRenderer::class)->render($template, ['customer_name' => 'Jane', 'invoice_number' => 'INV-1', 'amount_due' => 'AED 100']);

        $this->assertSame('Hello Jane', $rendered['subject']);
        $this->assertSame('<p>INV-1</p>', $rendered['body_html']);
        $this->assertSame('AED 100', $rendered['body_text']);
    }
}
