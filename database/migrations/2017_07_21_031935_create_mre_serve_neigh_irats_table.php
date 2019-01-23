<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMreServeNeighIratsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('MR_CZ')->create('mreServerNeighIrat', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('datetime_id');
            $table->string('ecgi', 30);
            $table->string('city', 50);
            $table->integer('mr_LteScEarfcn');
            $table->string('eventType',6);
            $table->integer('mr_GsmNcellBcch');
            $table->integer('mr_GsmNcellNcc');
            $table->integer('mr_GsmNcellBcc');
            $table->string('cell', 50);
            $table->string('cellId', 50);
            $table->string('cgi', 50);
            $table->integer('isdefined');
            $table->decimal('distance', 12, 3);
            $table->integer('sc_session_num');
            $table->integer('nc_session_num');
            $table->decimal('nc_session_num_avg', 12, 1);
            $table->integer('nc_session_num_max');
            $table->integer('nc_session_num_min');
            $table->decimal('nc_session_ratio', 3);
            $table->integer('sc_times_num');
            $table->integer('nc_times_num');
            $table->decimal('nc_times_ratio', 12, 3);
            $table->integer('ncTop2_times_num');
            $table->decimal('ncTop2_times_ratio', 12, 3);
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
        Schema::connection('MR_CZ')->drop('mreServerNeighIrat');
    }
}
