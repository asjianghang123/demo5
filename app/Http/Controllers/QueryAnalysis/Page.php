<?php

namespace App\Http\Controllers\QueryAnalysis;

use App\Http\Controllers\QueryAnalysis\Strategy\SqlStrategy;

class Page
{
	private static $strategy;

	private static $self;

	private static function index()
	{
		self::$strategy::$arr = [];
		//适配SN
		self::$strategy->diffSN();
		//适配site
		self::$strategy->diffSite();
		//适配其他
		self::$strategy->diffOther();

		return self::$strategy::$arr;
	}

	private static function setStrategy(SqlStrategy $strategy)
	{
		self::$strategy = $strategy;
	}

	public static function provinceMatch($province='jiangsu') {
		if( !self::$self ) {
			self::$self = new self;
		}
		$province = strtolower($province);
		if( $province === 'jiangsu' ){
			$strategy = new Strategy\JSSNStrategy();
		}elseif( $province === 'zhejiang' ) {
			$strategy = new Strategy\ZJSNStrategy();
		}else {
			$strategy = new Strategy\JSSNStrategy();
		}
		self::$self->setStrategy($strategy);
		return self::$self->index();
	}

}