<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;

class LogFulfilmentEvent
{
    public function handle(object $event): void
    {
        Log::info('Fulfilment domain event dispatched.', ['event' => $event::class]);
    }
}
