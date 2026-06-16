<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_counts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('status', 20)->default('open');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'status']);
        });

        Schema::create('inventory_count_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_count_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('counted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('system_quantity', 12, 3)->default(0);
            $table->decimal('counted_quantity', 12, 3)->nullable();
            $table->decimal('difference', 12, 3)->default(0);
            $table->string('sync_status', 20)->default('pending');
            $table->timestamp('counted_at')->nullable();
            $table->timestamps();
            $table->unique(['inventory_count_id', 'product_id']);
            $table->index(['inventory_count_id', 'sync_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_count_items');
        Schema::dropIfExists('inventory_counts');
    }
};
