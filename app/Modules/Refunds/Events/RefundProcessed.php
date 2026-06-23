<?php

namespace App\Modules\Refunds\Events;

use App\Modules\Refunds\Models\RefundRequest;

class RefundProcessed
{
    public function __construct(public RefundRequest $refundRequest) {}
}
