<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('claim_vouchers', function (Blueprint $table) {
            // Remove voucher_id column
            if (Schema::hasColumn('claim_vouchers', 'voucher_id')) {
                $table->dropColumn('voucher_id');
            }

            // Add points column after user_id
            if (!Schema::hasColumn('claim_vouchers', 'points')) {
                $table->integer('points')->after('user_id')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('claim_vouchers', function (Blueprint $table) {
            // Rollback changes
            if (!Schema::hasColumn('claim_vouchers', 'voucher_id')) {
                $table->bigInteger('voucher_id')->unsigned()->after('user_id');
            }

            if (Schema::hasColumn('claim_vouchers', 'points')) {
                $table->dropColumn('points');
            }
        });
    }
};
