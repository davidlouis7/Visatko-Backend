<?php

namespace App\Modules\Finance\Services;

class VatCalculator
{
    public function __construct(private readonly FinanceSettings $settings) {}

    /**
     * @param  array<int, array{description?: string, quantity?: numeric, unit_price?: numeric, discount_amount?: numeric}>  $items
     * @return array{subtotal: float, discount_total: float, taxable_amount: float, vat_rate: float, vat_amount: float, total: float, items: array<int, array<string, mixed>>}
     */
    public function calculate(array $items, ?bool $vatEnabled = null, ?float $vatRate = null): array
    {
        $rate = $vatRate ?? $this->settings->float('vat_rate');
        $enabled = $vatEnabled ?? $this->settings->bool('vat_enabled');
        $effectiveRate = $enabled ? $rate : 0.0;

        $lines = [];
        $subtotal = 0.0;
        $discountTotal = 0.0;
        $taxableAmount = 0.0;
        $vatAmount = 0.0;

        foreach ($items as $index => $item) {
            $quantity = $this->money((float) ($item['quantity'] ?? 1));
            $unitPrice = $this->money((float) ($item['unit_price'] ?? 0));
            $discount = $this->money((float) ($item['discount_amount'] ?? 0));
            $lineSubtotal = $this->money($quantity * $unitPrice);
            $lineTaxable = max(0.0, $this->money($lineSubtotal - $discount));
            $lineVat = $this->money($lineTaxable * ($effectiveRate / 100));
            $lineTotal = $this->money($lineTaxable + $lineVat);

            $lines[] = [
                'description' => $item['description'] ?? 'Invoice item '.($index + 1),
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_amount' => $discount,
                'vat_rate' => $effectiveRate,
                'vat_amount' => $lineVat,
                'line_subtotal' => $lineSubtotal,
                'line_total' => $lineTotal,
                'sort_order' => (int) ($item['sort_order'] ?? $index),
            ];

            $subtotal += $lineSubtotal;
            $discountTotal += $discount;
            $taxableAmount += $lineTaxable;
            $vatAmount += $lineVat;
        }

        return [
            'subtotal' => $this->money($subtotal),
            'discount_total' => $this->money($discountTotal),
            'taxable_amount' => $this->money($taxableAmount),
            'vat_rate' => $effectiveRate,
            'vat_amount' => $this->money($vatAmount),
            'total' => $this->money($taxableAmount + $vatAmount),
            'items' => $lines,
        ];
    }

    private function money(float $value): float
    {
        return round($value, 2);
    }
}
