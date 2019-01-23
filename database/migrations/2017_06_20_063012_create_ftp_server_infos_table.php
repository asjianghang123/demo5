<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFtpServerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ftpServerInfo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serverName', 255);
            $table->string('city', 255);
            $table->string('type', 255);
            $table->string('externalAddress', 255);
            $table->string('internalAddress', 255);
            $table->string('subNetwork', 255);
            $table->string('fileDir', 255);
            $table->string('userName', 255);
            $table->string('password', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ftpServerInfo');
    }
}
