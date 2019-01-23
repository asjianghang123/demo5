<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Alarm\FMA_alarm_list::class, function (Faker\Generator $faker) {
    return [
        'Log_record_id' => $faker->randomNumber(8),
        'Event_type' => $faker->randomNumber(8),
        'Event_time' => $faker->dateTimeThisMonth,
        'Trend_indication' => $faker->randomNumber(8),
        'Object_class' => $faker->randomNumber(8),
        'city' => $faker->city,
        'subNetwork' => $faker->word,
        'meContext' => $faker->word,
        'eutranCell' => $faker->word,
        'Object_of_reference' => $faker->word,
        'Perceived_severity' => $faker->randomNumber(8),
        'Probable_cause' => $faker->randomNumber(8),
        'Problem_data' => $faker->word,
        'Specific_problem' => $faker->randomNumber(8),
        'Correlated_record_name' => $faker->randomNumber(8),
        'Backup_object_instance' => $faker->word,
        'Backup_status' => $faker->randomNumber(8),
        'Proposed_repair_action' => $faker->randomNumber(8),
        'Object_type' => $faker->randomNumber(8),
        'Record_type' => $faker->randomNumber(8),
        'Alarm_number' => $faker->randomNumber(8),
        'Alarm_class' => $faker->randomNumber(8),
        'Alarm_category' => $faker->randomNumber(8),
        'Attendance_indication' => $faker->randomNumber(8),
        'Object_identifier' => $faker->randomNumber(8),
        'Rec_count' => $faker->randomNumber(8),
        'Problem_text' => $faker->word,
        'Cease_time' => $faker->dateTimeThisMonth,
        'Cease_record_type' => $faker->randomNumber(8),
        'Operator_name' => $faker->word,
        'Op_time' => $faker->dateTimeThisMonth,
        'Record_cease_marked' => $faker->randomNumber(8),
        'Alarm_id' => $faker->randomNumber(8),
        'Version' => $faker->randomNumber(8),
        'FMX_flag' => $faker->randomNumber(8),
        'TMOS_control_information' => $faker->word,
        'insert_time' => $faker->dateTimeThisMonth,
        'Previous_severity' => $faker->randomNumber(8),
        'Show_flag' => $faker->randomNumber(8),
        'FMX_generated' => $faker->randomNumber(8),
        'SP_text' => $faker->word,
        'PC_text' => $faker->word,
        'ET_text' => $faker->word,
        'extAlarmId' => $faker->word,
        'Alarm_state' => $faker->randomNumber(8)
    ];
});