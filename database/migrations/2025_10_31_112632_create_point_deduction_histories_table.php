<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointDeductionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_deduction_histories', function (Blueprint $table) {
            $table->id();
			$table->string('Admin_name');
			$table->string('Admin_type');
			$table->unsignedBigInteger('user_id');
			$table->string('deducted_points');
			$table->string('remaining_points');
			$table->string('total_points');
			$table->string('gross_remaining_points');
			$table->string('gross_total_points');
			$table->string('date_time');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');	
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_deduction_histories');
    }
}
