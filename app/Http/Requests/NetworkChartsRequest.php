<?php

/**
 * NetworkChartsRequest.php
 *
 * @category Requests
 * @package  App\Http\Requests
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/requests
 */
namespace App\Http\Requests;

/**
 * 指标概览请求
 * Class NetworkChartsRequest
 *
 * @category Requests
 * @package  App\Http\Requests
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/requests
 */
class NetworkChartsRequest extends Request
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
            // 'data.time'=>'required',
            // 'data.type'=>'required',
        ];
    }
}
