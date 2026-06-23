<?php

namespace Tests\Unit\Marketing;

use App\Modules\Marketing\Services\AttributionDataExtractor;
use Illuminate\Http\Request;
use Tests\TestCase;

class AttributionDataExtractorTest extends TestCase
{
    public function test_it_extracts_safe_attribution_fields(): void
    {
        $request = Request::create('/x', 'POST', ['utm_source' => 'meta', 'utm_medium' => 'cpc', 'ignored' => 'nope']);

        $this->assertSame(['utm_source' => 'meta', 'utm_medium' => 'cpc'], app(AttributionDataExtractor::class)->fromRequest($request));
    }
}
