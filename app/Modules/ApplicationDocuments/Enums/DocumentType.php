<?php

namespace App\Modules\ApplicationDocuments\Enums;

enum DocumentType: string
{
    case Passport = 'passport';
    case EmiratesId = 'emirates_id';
    case Photo = 'photo';
    case BankStatement = 'bank_statement';
    case SalaryCertificate = 'salary_certificate';
    case TenancyContract = 'tenancy_contract';
    case CarRegistration = 'car_registration';
    case TravelHistory = 'travel_history';
    case Other = 'other';
}
