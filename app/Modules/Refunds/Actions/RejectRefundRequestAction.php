<?php

namespace App\Modules\Refunds\Actions;

use App\Models\User;
use App\Modules\Refunds\Enums\RefundRequestStatus;
use App\Modules\Refunds\Models\RefundRequest;
use Illuminate\Validation\ValidationException;

class RejectRefundRequestAction
{
    public function execute(RefundRequest $refund, ?User $user = null, ?string $notes = null): RefundRequest
    {
        if ($refund->status !== RefundRequestStatus::Requested) {
            throw ValidationException::withMessages(['refund' => 'Only requested refunds can be rejected.']);
        }

        $refund->forceFill(['status' => RefundRequestStatus::Rejected, 'approved_by' => $user?->id, 'reviewed_at' => now(), 'internal_notes' => $notes ?? $refund->internal_notes])->save();

        return $refund->refresh();
    }
}
