<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateIRat4To2sTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('CDR_CZ')->create('irat4to2', function (Blueprint $table) {
            $table->date('date_id');
            $table->string('ecgi', 30);
            $table->string('cellName', 50);
            $table->string('cgi', 30);
            $table->string('cell', 50);
            $table->bigInteger('occurs');
            $table->string('isdefined', 10);
            $table->decimal('distince', 12, 2);
            $table->string('Longitude_4g', 30);
            $table->string('Latitude_4g', 30);
            $table->string('Longitude_2g', 30);
            $table->string('Latitude_2g', 30);
            $table->integer('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('CDR_CZ')->drop('irat4to2');
    }
}
