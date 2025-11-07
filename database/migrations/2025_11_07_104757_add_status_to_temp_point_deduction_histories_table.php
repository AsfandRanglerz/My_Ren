<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToTempPointDeductionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('temp_point_deduction_histories', function (Blueprint $table) {
        $table->string('status')->default('pending')->after('deducted_points');
    });
}

public function down()
{
    Schema::table('temp_point_deduction_histories', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}
}