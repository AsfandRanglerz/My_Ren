<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->double('profit_margin')->nullable()->after('points_per_sale');
			$table->double('discount')->nullable()->after('profit_margin');

        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['profit_margin', 'discount']);
        });
    }
};
