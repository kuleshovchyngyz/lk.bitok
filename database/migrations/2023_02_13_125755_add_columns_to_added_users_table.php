<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('added_users', function (Blueprint $table) {
            $table->string('passport_id')->nullable();
            $table->string('passport_authority')->nullable();
            $table->string('passport_authority_code')->nullable();
            $table->string('passport_issued_at')->nullable();
            $table->string('passport_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('added_users', function (Blueprint $table) {
            //
        });
    }
};
