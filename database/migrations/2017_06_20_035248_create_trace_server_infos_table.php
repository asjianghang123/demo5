<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTraceServerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('traceServerInfo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serverName',255);
            $table->string('city',255);
            $table->string('type',255);
            $table->string('ipAddress',255);
            $table->string('sshUserName',255);
            $table->string('sshPassword',255);
            $table->string('ftpUserName',255);
            $table->string('ftpPassword',255);
            $table->string('fileDir',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('traceServerInfo');
    }
}
