<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogServerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logServerInfo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nodName', 255);
            $table->string('ossAddress', 255);
            $table->string('ptnAddress', 255);
            $table->string('city', 255);
            $table->string('sshUserName', 255);
            $table->string('sshPassword', 255);
            $table->string('ftpUserName', 255);
            $table->string('ftpPassword', 255);
            $table->string('fileDir', 255);
            $table->string('logType', 255);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('logServerInfo');
    }
}
