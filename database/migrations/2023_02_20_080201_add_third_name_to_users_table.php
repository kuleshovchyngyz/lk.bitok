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
        Schema::table('black_lists', function (Blueprint $table) {
            $table->string('third_name')->nullable();
            $table->string('password_id')->nullable();
            $table->string('blacklist_log_id')->default(-1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('black_lists', function (Blueprint $table) {
            //
        });
    }
};
