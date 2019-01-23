<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateDatabaseConnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('mongs')->create('databaseconn', function (Blueprint $table) {
            $table->increments('id');
            $table->string('connName', 255);
            $table->string('cityChinese', 255);
            $table->string('host', 50);
            $table->string('port', 30);
            $table->string('dbName', 30);
            $table->string('userName', 30);
            $table->string('password', 40);
            $table->string('subNetwork', 500);
            $table->string('subNetworkFdd', 500);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('mongs')->drop('databaseconn');
    }
}
