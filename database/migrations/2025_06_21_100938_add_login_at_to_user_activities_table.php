<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLoginAtToUserActivitiesTable extends Migration
{
    public function up(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->timestamp('login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropColumn('login_at');
        });
    }

}
