<?php

namespace Tests\Unit\Finance;

use App\Modules\Finance\Services\FinanceSettings;
use App\Modules\Finance\Services\VatCalculator;
use Mockery;
use Tests\TestCase;

class VatCalculatorTest extends TestCase
{
    public function test_it_applies_discount_before_uae_vat_and_rounds_money(): void
    {
        $settings = Mockery::mock(FinanceSettings::class);
        $settings->shouldReceive('float')->with('vat_rate')->andReturn(5.0);
        $settings->shouldReceive('bool')->with('vat_enabled')->andReturn(true);

        $result = (new VatCalculator($settings))->calculate([
            ['description' => 'Service', 'quantity' => 2, 'unit_price' => 100, 'discount_amount' => 10],
        ]);

        $this->assertSame(200.0, $result['subtotal']);
        $this->assertSame(10.0, $result['discount_total']);
        $this->assertSame(190.0, $result['taxable_amount']);
        $this->assertSame(9.5, $result['vat_amount']);
        $this->assertSame(199.5, $result['total']);
    }
}
