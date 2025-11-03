<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('title');
            $table->enum('status', ['open', 'active', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');

            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                $table->json('impact_area')->nullable();
            } else {
                $table->polygon('impact_area')->nullable();
            }

            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('closed_at')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
