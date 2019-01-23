<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatabaseConn2GsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('databaseconn_2G', function (Blueprint $table) {
            $table->increments('id');
            $table->string('connName', 50);
            $table->string('cityChinese', 50);
            $table->string('host', 50);
            $table->string('port', 30);
            $table->string('dbName', 30);
            $table->string('userName', 30);
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
        Schema::drop('databaseconn_2G');
    }
}
