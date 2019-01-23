<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateTempEUtranCellRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('Global')->create('TempEUtranCellRelation', function (Blueprint $table) {
            $table->string('EUtranCellTDD', 255);
            $table->string('ecgi', 50);
            $table->string('city', 20);
            $table->string('EUtranCellRelationId', 255);
            $table->string('nc_cellName', 50);
            $table->string('sc_longitude', 30);
            $table->string('sc_latitude', 30);
            $table->integer('sc_dir');
            $table->integer('sc_channel');
            $table->string('sc_band', 20);
            $table->string('nc_longitude', 30);
            $table->string('nc_latitude', 30);
            $table->integer('nc_dir');
            $table->integer('nc_channel');
            $table->string('nc_band', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('Global')->dropIfExists('TempEUtranCellRelation');
    }
}
