<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('country')->nullable()->after('email'); 
        // after('email') optional — column order set کرنے کے لیے
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('country');
    });
}

}
