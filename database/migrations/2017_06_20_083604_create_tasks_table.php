<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function (Blueprint $table) {
            $table->string('taskName', 18);
            $table->string('status', 32);
            $table->timestamp('startTime');
            $table->timestamp('endTime');
            $table->string('tracePath', 500);
            $table->string('owner', 30);
            $table->timestamp('createTime');
            $table->string('type', 20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('task');
    }
}
