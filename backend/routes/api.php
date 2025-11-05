<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuditLogController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\OpsCenterSummaryController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\TrackingPingController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api'])
    ->prefix('tenants')
    ->group(function (): void {
        Route::get('/', [TenantController::class, 'index'])
            ->name('api.tenants.index');
        Route::post('/', [TenantController::class, 'store'])
            ->name('api.tenants.store');
        Route::get('{tenant}', [TenantController::class, 'show'])
            ->name('api.tenants.show');
        Route::patch('{tenant}', [TenantController::class, 'update'])
            ->name('api.tenants.update');
        Route::delete('{tenant}', [TenantController::class, 'destroy'])
            ->name('api.tenants.destroy');
    });

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
        Route::delete('incidents/{incident}', [IncidentController::class, 'destroy'])
            ->name('incidents.destroy');

        Route::get('tasks', [TaskController::class, 'index'])
            ->name('tasks.index');
        Route::get('tasks/{task}', [TaskController::class, 'show'])
            ->name('tasks.show');
        Route::post('tasks', [TaskController::class, 'store'])
            ->name('tasks.store');
        Route::patch('tasks/{task}', [TaskController::class, 'update'])
            ->name('tasks.update');
        Route::delete('tasks/{task}', [TaskController::class, 'destroy'])
            ->name('tasks.destroy');

        Route::get('inventories', [InventoryController::class, 'index'])
            ->name('inventories.index');
        Route::get('inventories/{inventory}', [InventoryController::class, 'show'])
            ->name('inventories.show');
        Route::post('inventories', [InventoryController::class, 'store'])
            ->name('inventories.store');
        Route::patch('inventories/{inventory}', [InventoryController::class, 'update'])
            ->name('inventories.update');
        Route::delete('inventories/{inventory}', [InventoryController::class, 'destroy'])
            ->name('inventories.destroy');

        Route::get('users', [UserController::class, 'index'])
            ->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])
            ->name('users.show');
        Route::post('users', [UserController::class, 'store'])
            ->name('users.store');
        Route::patch('users/{user}', [UserController::class, 'update'])
            ->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])
            ->name('users.destroy');

        Route::get('units', [UnitController::class, 'index'])
            ->name('units.index');
        Route::get('units/{unit}', [UnitController::class, 'show'])
            ->name('units.show');
        Route::post('units', [UnitController::class, 'store'])
            ->name('units.store');
        Route::patch('units/{unit}', [UnitController::class, 'update'])
            ->name('units.update');
        Route::delete('units/{unit}', [UnitController::class, 'destroy'])
            ->name('units.destroy');

        Route::get('opscenter/summary', [OpsCenterSummaryController::class, 'show'])
            ->name('opscenter.summary');

        Route::get('audit-logs', [AuditLogController::class, 'index'])
            ->name('audit-logs.index');

        Route::get('tracking/pings', [TrackingPingController::class, 'index'])
            ->name('tracking.pings.index');
        Route::get('tracking/pings/latest', [TrackingPingController::class, 'latest'])
            ->name('tracking.pings.latest');
        Route::post('tracking/pings', [TrackingPingController::class, 'store'])
            ->name('tracking.pings.store');
    });
