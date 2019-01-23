<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteLtesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siteLte', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ecgi', 50);
            $table->string('cellName', 50);
            $table->string('cellNameChinese', 255);
            $table->string('siteName', 50);
            $table->string('siteNameChinese', 255);
            $table->string('duplexMode', 10);
            $table->integer('rsi');
            $table->integer('tac');
            $table->string('longitudeBD', 30);
            $table->string('latitudeBD', 30);
            $table->string('longitude', 30);
            $table->string('latitude', 30);
            $table->integer('dir');
            $table->integer('pci');
            $table->integer('earfcn');
            $table->string('siteType', 20);
            $table->string('cellType', 20);
            $table->integer('tiltM');
            $table->integer('tiltE');
            $table->integer('antHeight');
            $table->string('dualBandNetwork', 10);
            $table->string('CANetwork', 10);
            $table->string('address', 20);
            $table->string('band', 20);
            $table->integer('channelBandWidth');
            $table->string('noofTxAntennas(Site)', 255);
            $table->string('highTraffic', 10);
            $table->string('highInterference', 10);
            $table->string('HST', 10);
            $table->string('cluster', 50);
            $table->string('subNetwork', 50);
            $table->string('currentOSS', 50);
            $table->string('覆盖属性', 255);
            $table->string('city', 20);
            $table->dateTime('importDate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('siteLte');
    }
}
