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
            $table->boolean('verification')->default(false);
            $table->integer('sanction')->default(0);
            $table->timestamp('verification_date')->default(now());
        });
    }

    public function down()
    {
        Schema::table('added_users', function (Blueprint $table) {
            $table->dropColumn('verification');
            $table->dropColumn('sanction');
            $table->dropColumn('verification_date');
        });
    }
};
