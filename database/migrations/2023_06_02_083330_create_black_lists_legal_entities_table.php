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
        Schema::create('black_lists_legal_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('blacklist_log_id')->nullable();
            $table->string('hash')->nullable();
            $table->string('country_id')->default(1);
            $table->string('country')->nullable();
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
        Schema::dropIfExists('black_lists_legal_entities');
    }
};
