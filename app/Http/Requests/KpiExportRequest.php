<?php

/**
 * KpiExportRequest.php
 *
 * @category Requests
 * @package  App\Http\Requests
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/requests
 */
namespace App\Http\Requests;

/**
 * KPI导出请求
 * Class KpiExportRequest
 *
 * @category Requests
 * @package  App\Http\Requests
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/requests
 */
class KpiExportRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }
}
