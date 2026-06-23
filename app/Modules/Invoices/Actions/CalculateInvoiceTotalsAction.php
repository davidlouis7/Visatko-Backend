<?php

namespace App\Modules\Invoices\Actions;

use App\Modules\Finance\Services\VatCalculator;

class CalculateInvoiceTotalsAction
{
    public function __construct(private readonly VatCalculator $calculator) {}

    /** @param array<int, array<string, mixed>> $items */
    public function execute(array $items): array
    {
        return $this->calculator->calculate($items);
    }
}
