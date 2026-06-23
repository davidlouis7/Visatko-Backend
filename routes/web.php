<?php

use App\Support\OpenApi\OpenApiController;
use Illuminate\Support\Facades\Route;

Route::get('/api/documentation', OpenApiController::class);
Route::get('/openapi.yaml', fn () => response(file_get_contents(base_path('docs/openapi/openapi.yaml')), 200, ['Content-Type' => 'application/yaml']));

Route::get('/', function () {
    return view('welcome');
});
