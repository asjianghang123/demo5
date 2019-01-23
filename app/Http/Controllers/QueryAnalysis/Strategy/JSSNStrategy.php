<?php

namespace App\Http\Controllers\QueryAnalysis\Strategy;

class JSSNStrategy implements SqlStrategy
{
	public static $arr;
	public function diffSN()
	{
		self::$arr['SN'] = " substring(SN,charindex('=',substring(SN,32,25))+32,charindex(',',substring(SN,32,25))-charindex('=',substring(SN,32,25))-1) ";
	}

	public function diffSite()
	{
		self::$arr['site'] = " substring(substring(SN,charindex (',', substring(SN, 32, 25)) + 32),11,25) ";
	}

	public function diffOther()
	{
		self::$arr['other'] = "other";
	}
}