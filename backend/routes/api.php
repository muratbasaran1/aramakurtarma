<?php

declare(strict_types=1);

use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'tenant'])
    ->prefix('tenants/{tenant}')
    ->as('api.tenants.')
    ->group(function (): void {
        Route::get('incidents', [IncidentController::class, 'index'])
            ->name('incidents.index');
        Route::get('incidents/{incident}', [IncidentController::class, 'show'])
            ->name('incidents.show');
        Route::post('incidents', [IncidentController::class, 'store'])
            ->name('incidents.store');
        Route::patch('incidents/{incident}', [IncidentController::class, 'update'])
            ->name('incidents.update');

        Route::get('tasks', [TaskController::class, 'index'])
            ->name('tasks.index');
        Route::get('tasks/{task}', [TaskController::class, 'show'])
            ->name('tasks.show');
        Route::post('tasks', [TaskController::class, 'store'])
            ->name('tasks.store');
        Route::patch('tasks/{task}', [TaskController::class, 'update'])
            ->name('tasks.update');

        Route::get('inventories', [InventoryController::class, 'index'])
            ->name('inventories.index');
        Route::get('inventories/{inventory}', [InventoryController::class, 'show'])
            ->name('inventories.show');
        Route::post('inventories', [InventoryController::class, 'store'])
            ->name('inventories.store');
        Route::patch('inventories/{inventory}', [InventoryController::class, 'update'])
            ->name('inventories.update');

        Route::get('users', [UserController::class, 'index'])
            ->name('users.index');

        Route::get('units', [UnitController::class, 'index'])
            ->name('units.index');
        Route::get('units/{unit}', [UnitController::class, 'show'])
            ->name('units.show');
    });
