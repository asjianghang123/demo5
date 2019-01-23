<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMreServerNeighIratDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('MR_CZ')->create('mreServerNeighIrat_day', function (Blueprint $table) {
            $table->increments('id');
            $table->date('dateId');
            $table->string('ecgi', 30);
            $table->string('cellName', 50);
            $table->string('siteName', 50);
            $table->integer('mr_LteScEarfcn');
            $table->string('eventType', 6);
            $table->integer('mr_GsmNcellBcch');
            $table->integer('mr_GsmNcellNcc');
            $table->integer('mr_GsmNcellBcc');
            $table->string('cell', 50);
            $table->string('cellID', 50);
            $table->string('cgi', 50);
            $table->integer('isdefined');
            $table->decimal('distance', 12, 3);
            $table->string('longitude_4g', 30);
            $table->string('latitude_4g', 30);
            $table->string('longitude_2g', 30);
            $table->string('latitude_2g', 30);
            $table->integer('sc_session_num');
            $table->integer('nc_session_num');
            $table->decimal('nc_session_ratio', 12, 2);
            $table->integer('sc_times_num');
            $table->integer('nc_times_num');
            $table->decimal('nc_times_ratio', 12, 2);
            $table->decimal('avg_mr_LteScRSRP', 12, 3);
            $table->decimal('avg_mr_LteScRSRQ', 12, 3);
            $table->decimal('avg_mr_GsmNcellCarrierRSSI', 12, 3);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('MR_CZ')->drop('mreServerNeighIrat_day');
    }
}
