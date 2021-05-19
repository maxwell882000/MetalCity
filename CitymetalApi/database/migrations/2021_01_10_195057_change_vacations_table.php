<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeVacationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->dropColumn('price');
        });
        Schema::table('vacations', function (Blueprint $table) {
            $table->bigInteger('price')->default(0);
            $table->integer('unit')->default(-1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('unit');
        });
        Schema::table('vacations', function (Blueprint $table) {
            $table->string('price');
        });
    }
}
