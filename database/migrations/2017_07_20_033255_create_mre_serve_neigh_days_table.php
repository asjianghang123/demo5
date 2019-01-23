<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMreServeNeighDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('MR_CZ')->create('mreServeNeigh_day', function (Blueprint $table) {
            $table->increments('id');
            $table->date('dateId');
            $table->string('ecgi', 30);
            $table->decimal('longitude', 12, 6);
            $table->decimal('latitude', 12, 6);
            $table->integer('mr_LteScEarfcn');
            $table->integer('mr_LteNcEarfcn');
            $table->bigInteger('mr_LteNcPci');
            $table->string('eventType', 6);
            $table->string('ecgiNeigh_direct', 50);
            $table->integer('isdefined_direct');
            $table->string('distance_direct', 50);
            $table->integer('ncFreq_session');
            $table->integer('nc_session_num');
            $table->decimal('nc_session_ratio', 12, 2);
            $table->integer('ncFreq_times_num');
            $table->integer('nc_times_num');
            $table->decimal('nc_times_ratio', 12, 2);
            $table->decimal('avg_mr_LteScRSRP', 12, 3);
            $table->decimal('avg_mr_LteScRSRQ', 12, 3);
            $table->decimal('avg_mr_LteNcRSRP', 12, 3);
            $table->decimal('avg_mr_LteNcRSRQ', 12, 3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('MR_CZ')->drop('mreServeNeigh_day');
    }
}
