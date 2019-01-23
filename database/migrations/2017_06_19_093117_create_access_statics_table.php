<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAccessStaticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('access_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('date_id',255);
            $table->string('user',255);
            $table->string('url',255);
            $table->string('urlChinese',255);
            $table->dateTime('createTime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('access_detail');
    }
}
