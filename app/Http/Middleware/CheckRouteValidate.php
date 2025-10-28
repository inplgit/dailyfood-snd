<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Route;
use App\Models\RouteDay;

class CheckRouteValidate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

       // $route_day = RouteDay::where('route_id' , $request->route_id)->where('day' , date('l'))->first();
		

	$route_day = RouteDay::where('route_id', $request->route_id)->whereRaw("FIND_IN_SET(?, REPLACE(day, ' ', ''))", [date('l')])->first();



        if ($route_day) {
            return $next($request);
        }
        else{
            $response = [
                'success' => false,
                'message' => 'Server Error',
                'data' => 'Route Not Match',
            ];
            return response()->json($response, 404);
        }
    }
}
