<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherIdToWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('withdraw_requests', function (Blueprint $table) {
        $table->unsignedBigInteger('voucher_id')->nullable()->after('user_id');

        // Foreign key constraint
        $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('withdraw_requests', function (Blueprint $table) {
        $table->dropForeign(['voucher_id']);
        $table->dropColumn('voucher_id');
    });
}
}
