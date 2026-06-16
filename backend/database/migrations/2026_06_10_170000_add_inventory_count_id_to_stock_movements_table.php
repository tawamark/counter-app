<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('inventory_count_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
            $table->index(['company_id', 'inventory_count_id']);
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'inventory_count_id']);
            $table->dropConstrainedForeignId('inventory_count_id');
        });
    }
};
