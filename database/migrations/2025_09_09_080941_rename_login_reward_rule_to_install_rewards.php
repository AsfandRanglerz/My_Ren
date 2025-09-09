<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameLoginRewardRuleToInstallRewards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('login_reward_rule', function (Blueprint $table) {
            //
             $table->string('target_sales')->nullable(); // pehle day tha
            $table->integer('points');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('login_reward_rule', function (Blueprint $table) {
            //
        });
    }
}
