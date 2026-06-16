<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'name']);
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('cnpj', 20)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
            $table->unique(['company_id', 'cnpj']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku');
            $table->string('barcode')->nullable();
            $table->string('unit', 20)->default('un');
            $table->decimal('cost_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->decimal('current_quantity', 12, 3)->default(0);
            $table->timestamps();
            $table->unique(['company_id', 'sku']);
            $table->index(['company_id', 'barcode']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20);
            $table->decimal('quantity', 12, 3);
            $table->decimal('quantity_before', 12, 3);
            $table->decimal('quantity_after', 12, 3);
            $table->string('reason')->nullable();
            $table->timestamps();
            $table->index(['company_id', 'type']);
            $table->index(['company_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
    }
};
