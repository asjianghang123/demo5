<?php
	
namespace App\Http\Controllers\QueryAnalysis\Strategy;

interface SqlStrategy
{
	//根据城市适配不同的SN
	function diffSN();

	//根据城市适配不同的site
	function diffSite();
	
	//适配其他
	function diffOther();
}