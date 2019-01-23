<?php

/**
 * ScaleExportRequest.php
 *
 * @category Requests
 * @package  App\Http\Requests
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/requests
 */
namespace App\Http\Requests;

/**
 * 规模概览请求
 * Class ScaleExportRequest
 *
 * @category Requests
 * @package  App\Http\Requests
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/requests
 */
class ScaleExportRequest extends Request
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
