<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateA2ThresholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('MR_CZ')->create('mreA2Threshold', function (Blueprint $table) {
            $table->timestamp('datetime_id');
            $table->string('ecgi', 30);
            $table->string('city', 255);
            $table->integer('mr_LteScRSRQ_No90_percent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('MR_CZ')->drop('mreA2Threshold');
    }
}
