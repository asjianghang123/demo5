<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use App\Models\Mongs\NeighOptimizationWhiteList;

class CreateNeighOptimizationWhiteListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('NeighOptimizationWhiteList', function (Blueprint $table) {
            $table->string('subNetwork', 255);
            $table->string('site', 255);
            $table->string('EUtranCellTDD', 255);
            $table->string('ecgi', 255);
            $table->string('city', 255);
            $table->string('dataType', 255);
            $table->string('OptimizationType', 255);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('NeighOptimizationWhiteList');
    }
}
