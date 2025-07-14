<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpTokenToEmailOtpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('email_otps', function (Blueprint $table) {
        $table->uuid('otp_token')->nullable()->after('otp');
        // `after('otp')` optional ہے — column order fix کرنے کے لیے
    });
}

public function down()
{
    Schema::table('email_otps', function (Blueprint $table) {
        $table->dropColumn('otp_token');
    });
}

}
