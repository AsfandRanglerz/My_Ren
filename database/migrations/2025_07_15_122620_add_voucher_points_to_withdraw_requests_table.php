<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherPointsToWithdrawRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('withdraw_requests', function (Blueprint $table) {
        $table->integer('voucher_points')->nullable()->after('withdrawal_amount');
    });
}

public function down()
{
    Schema::table('withdraw_requests', function (Blueprint $table) {
        $table->dropColumn('voucher_points');
    });
}

}
