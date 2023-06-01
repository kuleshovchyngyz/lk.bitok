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
        Schema::create('legal_entities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('country_id')->default(1);
            $table->string('address')->nullable();
            $table->string('director_full_name')->nullable();
            $table->dateTime('birth_date')->nullable();
            $table->timestamp('verification_date')->nullable();
            $table->integer('sanction')->default(0);
            $table->string('hash')->nullable();
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
        Schema::dropIfExists('legal_entities');
    }
};
