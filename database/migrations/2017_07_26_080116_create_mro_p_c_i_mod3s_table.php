<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateMroPCIMod3sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('MR_CZ')->create('mroPciMod3', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('datetime_id');
            $table->string('userLabel', 50);
            $table->string('ecgi', 50);
            $table->string('EutrancellTddName', 50);
            $table->integer('mr_LteScEarfcn');
            $table->integer('mr_LteScPci');
            $table->integer('mr_LteNcPci');
            $table->string('ecgiNeigh', 50);
            $table->decimal('distance', 12, 2);
            $table->integer('mr_LteNcEarfcn');
            $table->integer('mr_LteScPciMod3');
            $table->integer('mr_LteNcPciMod3');
            $table->bigInteger('CountOfOverlapSample');
            $table->bigInteger('TotalNumOfSample');
            $table->decimal('OverlapRate', 24, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('MR_CZ')->dropIfExists('mroPciMod3');
    }
}
