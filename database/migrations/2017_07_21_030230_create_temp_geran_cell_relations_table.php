<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempGeranCellRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('Global')->create('TempGeranCellRelation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ecgi', 50);
            $table->string('city', 20);
            $table->string('GeranCellRelationId', 255);
            $table->string('sc_longitude', 30);
            $table->string('sc_latitude', 30);
            $table->integer('sc_dir');
            $table->integer('sc_channel');
            $table->string('sc_band', 20);
            $table->string('nc_longitude', 30);
            $table->string('nc_latitude', 30);
            $table->integer('nc_dir');
            $table->integer('nc_ARFCN');
            $table->integer('nc_BAND');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('Global')->drop('TempGeranCellRelation');
    }
}
