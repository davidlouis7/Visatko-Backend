<?php

namespace App\Modules\VisaApplications\Actions;

use App\Modules\CRM\Actions\AddTimelineEntry;
use App\Modules\Customers\Actions\ResolveCustomer;
use App\Modules\VisaApplications\Enums\ApplicationStatus;
use App\Modules\VisaApplications\Enums\PaymentStatus;
use App\Modules\VisaApplications\Events\VisaApplicationCreated;
use App\Modules\VisaApplications\Models\VisaApplication;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateVisaApplicationAction
{
    public function __construct(private ResolveCustomer $customers, private AddTimelineEntry $timeline) {}

    public function execute(array $attributes, ?int $consultationId = null): VisaApplication
    {
        $application = DB::transaction(function () use ($attributes, $consultationId): VisaApplication {
            $customer = $this->customers->execute($attributes);
            $application = VisaApplication::query()->create([...Arr::except($attributes, ['preferred_language']), 'application_number' => 'VSA-'.now()->format('Ym').'-'.strtoupper(Str::random(8)), 'customer_id' => $customer->id, 'consultation_id' => $consultationId, 'status' => ApplicationStatus::New, 'payment_status' => PaymentStatus::Unpaid, 'submitted_at' => now()]);
            $this->timeline->execute($application, 'application_created', 'Visa application created');

            return $application;
        });
        event(new VisaApplicationCreated($application));

        return $application;
    }
}
