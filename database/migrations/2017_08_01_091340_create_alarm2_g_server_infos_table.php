<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateAlarm2GServerInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alarm2GServerInfo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serverName', 50);
            $table->string('city', 50);
            $table->string('host', 50);
            $table->string('port', 30);
            $table->string('dbName', 30);
            $table->string('userName', 40);
            $table->string('password', 40);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('alarm2GServerInfo');
    }
}
