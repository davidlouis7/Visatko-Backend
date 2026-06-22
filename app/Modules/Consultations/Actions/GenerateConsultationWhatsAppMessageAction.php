<?php

namespace App\Modules\Consultations\Actions;

use App\Modules\Consultations\Models\Consultation;

class GenerateConsultationWhatsAppMessageAction
{
    public function execute(Consultation $consultation): string
    {
        $yesNo = fn (?bool $value): string => is_null($value) ? 'N/A' : ($value ? 'Yes' : 'No');
        $lines = ['New Visatko consultation', "Reference: {$consultation->public_id}", "Name: {$consultation->full_name}", "Phone: {$consultation->phone}", "WhatsApp: {$consultation->whatsapp_number}", 'Email: '.($consultation->email ?: 'N/A'), 'Nationality: '.($consultation->nationality ?: 'N/A'), 'Emirate: '.($consultation->current_emirate ?: 'N/A'), 'Destination: '.($consultation->destinationCountry?->translationFor()?->name ?: 'N/A'), 'Visa service: '.($consultation->preferredService?->translationFor()?->title ?: 'N/A'), 'Residing in UAE: '.$yesNo($consultation->are_you_residing_in_uae), "Monthly salary: {$consultation->monthly_salary_range}", 'Salary transfer: '.$yesNo($consultation->salary_transferred_regularly), 'Tenancy contract: '.$yesNo($consultation->has_tenancy_contract), 'Owns car: '.$yesNo($consultation->owns_car), 'Travel history: '.$yesNo($consultation->has_previous_travel_history), 'Previous refusal: '.$yesNo($consultation->previous_visa_refusal), 'Expected travel: '.($consultation->expected_travel_date?->format('Y-m-d') ?? 'N/A'), 'Notes: '.($consultation->notes ?: 'N/A')];
        $number = preg_replace('/\D+/', '', (string) config('services.whatsapp.number'));

        return 'https://wa.me/'.$number.'?text='.rawurlencode(implode("\n", $lines));
    }
}
