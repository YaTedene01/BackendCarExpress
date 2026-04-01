<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs/openapi.json', function () {
    $path = storage_path('api-docs/api-docs.json');

    abort_unless(File::exists($path), 404, 'Documentation OpenAPI introuvable.');

    $payload = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
    $payload['servers'] = [[
        'url' => rtrim(config('app.url'), '/'),
        'description' => 'Serveur API',
    ]];

    return response()->json($payload);
})->name('swagger.openapi');
