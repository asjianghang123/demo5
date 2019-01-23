<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlarmInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('AlarmInfo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('alarmNameE', 20);
            $table->string('alarmNameC', 20);
            $table->string('levelE', 20);
            $table->string('levelC', 20);
            $table->string('interfere', 20);
            $table->string('access', 20);
            $table->string('lost', 20);
            $table->string('handover', 20);
            $table->string('comments', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('AlarmInfo');
    }
}
