<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSeenToClaimVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('claim_vouchers', function (Blueprint $table) {
        $table->boolean('is_seen')->default(0); // 0 = unseen, 1 = seen
    });
}

public function down()
{
    Schema::table('claim_vouchers', function (Blueprint $table) {
        $table->dropColumn('is_seen');
    });
}

}
