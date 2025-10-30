<?php

declare(strict_types=1);

use App\Http\Controllers\OpsCenterController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/opscenter', [OpsCenterController::class, 'index'])
    ->name('opscenter');
