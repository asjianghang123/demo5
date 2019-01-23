<?php

namespace App\Http\Controllers\QueryAnalysis\Strategy;

class ZJSNStrategy implements SqlStrategy
{
	public static $arr;
	public function diffSN()
	{
		self::$arr['SN'] = " substring(SN, charindex ('=', SN)+1, charindex (',', SN)-12) ";
	}

	public function diffSite()
	{
		self::$arr['site'] = " substring(SN, charindex ('=', SN)+1, charindex (',', SN)-12) ";
	}

	public function diffOther()
	{
		self::$arr['other'] = "other";
	}
}