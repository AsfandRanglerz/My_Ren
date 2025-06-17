<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointsPerSaleToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('products', function (Blueprint $table) {
        $table->integer('points_per_sale')->default(0)->after('price'); // after price, optional
    });
}

public function down()
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn('points_per_sale');
    });
}

}
