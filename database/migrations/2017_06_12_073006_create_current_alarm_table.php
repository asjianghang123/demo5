<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCurrentAlarmTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('alarm')->create('FMA_alarm_list', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('Log_record_id');
            $table->integer('Event_type');
            $table->dateTime('Event_time');
            $table->integer('Trend_indication');
            $table->integer('Object_class');
            $table->string('city', 25);
            $table->string('subNetwork', 25);
            $table->string('meContext', 25);
            $table->string('eutranCell', 25);
            $table->string('Object_of_reference', 255);
            $table->integer('Perceived_severity');
            $table->integer('Probable_cause');
            $table->string('Problem_data', 255);
            $table->integer('Specific_problem');
            $table->integer('Correlated_record_name');
            $table->string('Backup_object_instance', 255);
            $table->integer('Backup_status');
            $table->integer('Proposed_repair_action');
            $table->integer('Object_type');
            $table->integer('Record_type');
            $table->integer('Alarm_number');
            $table->integer('Alarm_class');
            $table->integer('Alarm_category');
            $table->integer('Attendance_indication');
            $table->integer('Object_identifier');
            $table->integer('Rec_count');
            $table->string('Problem_text', 255);
            $table->dateTime('Cease_time');
            $table->integer('Cease_record_type');
            $table->string('Operator_name', 255);
            $table->dateTime('Op_time');
            $table->integer('Record_cease_marked');
            $table->integer('Alarm_id');
            $table->integer('Version');
            $table->integer('FMX_flag');
            $table->string('TMOS_control_information', 255);
            $table->dateTime('insert_time');
            $table->integer('Previous_severity');
            $table->integer('Show_flag');
            $table->integer('FMX_generated');
            $table->string('SP_text', 255);
            $table->string('PC_text', 255);
            $table->string('ET_text', 50);
            $table->string('extAlarmId', 255);
            $table->integer('Alarm_state');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('alarm')->drop('FMA_alarm_list');
    }
}
