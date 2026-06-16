<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'barcode']);
            $table->unique(['company_id', 'barcode']);
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'barcode']);
            $table->index(['company_id', 'barcode']);
        });
    }
};
