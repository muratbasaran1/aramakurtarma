<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_unit_id')->nullable()->constrained('units')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['planned', 'assigned', 'in_progress', 'done', 'verified'])->default('planned');

            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->json('route')->nullable();
            } else {
                $table->lineString('route')->nullable();
            }

            $table->boolean('requires_double_confirmation')->default(true);
            $table->timestampTz('planned_start_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('verified_at')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['incident_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
