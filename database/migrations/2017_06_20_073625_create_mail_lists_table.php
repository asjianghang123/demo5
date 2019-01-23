<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mail_list', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mailAddress',255);
            $table->string('name',255);
            $table->string('role',255);
            $table->string('scope',255);
            $table->string('city',255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mail_list');
    }
}
