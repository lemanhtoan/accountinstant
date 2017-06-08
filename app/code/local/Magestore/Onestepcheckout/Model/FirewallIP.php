<?php namespace mobi\Http\Middleware;
use Closure;
use Redirect;
class FirewallIP{
	public function handle($request, Closure $next)
	{
		$ip = $request->getClientIp();
		$ips = explode('.',$ip);
		$whiteListIP = [
			'171.253.46.249',
			'27.75.176.246',
			'115.73.95.146',
			'115.77.140.64',
			'115.77'
		];
		foreach ($whiteListIP as $value) {
			$ipl = explode('.',$value);
			if(count($ipl) == 2){
				if($ips[0] == $ipl[0] && $ips[1] == $ipl[1]){
					return $next($request);
				}
			}
			if(count($ipl) == 3){
				if($ips[0] == $ipl[0] && $ips[1] == $ipl[1] && $ips[2] == $ipl[2]){
					return $next($request);
				}
			}
			if(count($ipl) == 4){
				if($ips[0] == $ipl[0] && $ips[1] == $ipl[1] && $ips[2] == $ipl[2] && $ips[3] == $ipl[3]){
					return $next($request);
				}
			}
		}
		return Redirect::to('');
	}
}
