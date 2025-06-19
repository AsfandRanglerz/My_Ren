<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // 🔗 Foreign Keys
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('product_id');

            // 🎯 Core Fields
            $table->string('scan_code')->unique(); // Each scan_code must be unique
            $table->integer('points_earned')->default(0); // Based on product.points_per_sale

            // 🕒 Timestamps
            $table->timestamps();

            // 🔐 Define Foreign Key Constraints (optional but recommended)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
}