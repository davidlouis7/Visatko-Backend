<?php

namespace App\Modules\Consultations\Enums;

enum ConsultationStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case NotQualified = 'not_qualified';
    case ConvertedToApplication = 'converted_to_application';
    case Closed = 'closed';
}
