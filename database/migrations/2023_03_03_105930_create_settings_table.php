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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->integer('limit')->default(10000000);
            $table->string('usd_to_som')->default(1);
            $table->string('usdt_to_som')->default(1);
            $table->string('rub_to_som')->default(1);
            $table->timestamp('high_risk')->default(Carbon\Carbon::now()->addYear());
            $table->timestamp('risk')->default(Carbon\Carbon::now()->addYear());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
