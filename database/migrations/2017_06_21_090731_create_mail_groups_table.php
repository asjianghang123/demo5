<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('scope', 255);
            $table->string('scopeName', 255);
            $table->string('role', 255);
            $table->string('roleName', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mail_group');
    }
}
