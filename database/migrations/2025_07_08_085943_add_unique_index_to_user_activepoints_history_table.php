<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('user_activepoints_history', function (Blueprint $table) {
            $table->unique(['user_id', 'source', 'day_counter'], 'unique_user_source_day');
        });
    }

    public function down()
    {
        Schema::table('user_activepoints_history', function (Blueprint $table) {
            $table->dropUnique('unique_user_source_day');
        });
    }
};
