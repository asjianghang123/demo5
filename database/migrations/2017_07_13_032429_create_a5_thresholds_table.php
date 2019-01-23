<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateA5ThresholdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('MR_CZ')->create('mreA5Threshold', function (Blueprint $table) {
            $table->timestamp('datetime_id');
            $table->string('ecgi', 30);
            $table->integer('mr_LteNcEarfcn');
            $table->decimal('mr_LteScRSRQ_No90_percent', 10, 2);
            $table->decimal('mr_LteNcRSRQ_No90_percent', 10, 2);
            $table->string('comments', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('MR_CZ')->drop('mreA5Threshold');
    }
}
