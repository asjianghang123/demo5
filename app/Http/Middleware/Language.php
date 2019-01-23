<?php

namespace App\Http\Middleware;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Session\Middleware\StartSession as StartSession;
use Closure;
use App;
use Config;

class Language extends StartSession
{
	public function handle($request, Closure $next) 
	{
		
		if($request->session()->has('language')) {
			// $request->session()->forget('language');
			App::setlocale($request->session()->get('language'));
			// print_r($request->session()->all());
		}else {
			// print_r(App::setlocale(Config::get('app.fallback_locale')));
			App::setlocale(Config::get('app.locale'));
		}
		return parent::handle($request, $next);
		// return $next->($request);
	}
}