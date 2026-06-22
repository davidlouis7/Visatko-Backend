<?php

namespace App\Http\Middleware;

use App\Modules\Languages\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $requested = $request->query('locale');

        if (! is_string($requested) || $requested === '') {
            $requested = explode('-', explode(',', (string) $request->header('Accept-Language', ''))[0])[0];
        }

        $language = Language::query()->where('code', $requested)->where('is_active', true)->first()
            ?? Language::query()->where('is_default', true)->where('is_active', true)->first();
        $locale = $language?->code ?? config('app.fallback_locale', 'en');

        app()->setLocale($locale);
        $request->attributes->set('locale', $locale);

        return $next($request);
    }
}
