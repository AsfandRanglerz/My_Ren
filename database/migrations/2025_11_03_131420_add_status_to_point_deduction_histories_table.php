<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToPointDeductionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::table('point_deduction_histories', function (Blueprint $table) {
        $table->string('status')->default('pending')->after('id'); // 'id' ke baad add hoga
    });
}

public function down(): void
{
    Schema::table('point_deduction_histories', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

}
