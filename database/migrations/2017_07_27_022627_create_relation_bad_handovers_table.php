<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateRelationBadHandoversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('autokpi')->create('RelationBadHandover', function (Blueprint $table) {
            $table->string('day_from', 11);
            $table->string('day_to', 11);
            $table->string('city', 25);
            $table->string('subNetwork', 25);
            $table->string('cell', 25);
            $table->string('EutranCellRelation', 255);
            $table->decimal('切换成功率', 18, 2);
            $table->decimal('同频切换成功率', 18, 2);
            $table->decimal('异频切换成功率', 18, 2);
            $table->decimal('同频准备切换尝试数', 18, 0);
            $table->decimal('同频准备切换成功数', 18, 0);
            $table->decimal('同频执行切换尝试数', 18, 0);
            $table->decimal('同频执行切换成功数', 18, 0);
            $table->decimal('异频准备切换尝试数', 18, 0);
            $table->decimal('异频准备切换成功数', 18, 0);
            $table->decimal('异频执行切换尝试数', 18, 0);
            $table->decimal('异频执行切换成功数', 18, 0);
            $table->decimal('准备切换成功率', 18, 2);
            $table->decimal('执行切换成功率', 18, 2);
            $table->decimal('准备切换尝试数', 18, 0);
            $table->decimal('准备切换成功数', 18, 0);
            $table->decimal('准备切换失败数', 18, 0);
            $table->decimal('执行切换尝试数', 18, 0);
            $table->decimal('执行切换成功数', 18, 0);
            $table->decimal('执行切换失败数', 18, 0);
            $table->string('mlongitude', 30);
            $table->string('mlatitude', 30);
            $table->integer('mdir');
            $table->string('mband', 20);
            $table->string('slongitude', 30);
            $table->string('slatitude', 30);
            $table->integer('sdir');
            $table->string('sband', 20);
            $table->string('scell', 25);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('autokpi')->dropIfExists('RelationBadHandover');
    }
}
