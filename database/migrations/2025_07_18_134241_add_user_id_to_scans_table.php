<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToScansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('scans', function (Blueprint $table) {
        $table->unsignedBigInteger('user_id')->nullable()->after('id'); // ya kisi aur column ky baad
    });
}


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scans', function (Blueprint $table) {
            //
        });
    }
}
