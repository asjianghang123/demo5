<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteGSMsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('siteGsm', function (Blueprint $table) {
            $table->increments('id');
            $table->string('CELL',50);
            $table->string('CellNameChinese',100);
            $table->integer('CellIdentity');
            $table->integer('BAND');
            $table->integer('ARFCN');
            $table->string('LongitudeBD',30);
            $table->string('LatitudeBD',30);
            $table->string('Longitude',30);
            $table->string('Latitude',30);
            $table->integer('plmnIdentity_mcc');
            $table->integer('plmnIdentity_mnc');
            $table->integer('LAC');
            $table->integer('BCCH');
            $table->integer('BCC');
            $table->integer('NCC');
            $table->string('dtmSupport',50);
            $table->string('city',20);
            $table->dateTime('importDate');
            $table->integer('dir');
            $table->integer('height');
            $table->string('cellType',30);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('siteGsm');
    }
}
