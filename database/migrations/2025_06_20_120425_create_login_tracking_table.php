<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginTrackingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('login_tracking', function (Blueprint $table) {
            $table->id();
        $table->unsignedBigInteger('user_id');
           $table->timestamp('login_at')->nullable();
        $table->timestamp('logout_at')->nullable();
            $table->timestamps();
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('login_tracking');
    }
}
