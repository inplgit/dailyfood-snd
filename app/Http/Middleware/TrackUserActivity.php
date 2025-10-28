<?php

namespace App\Http\Middleware;
use App\Models\UserActivity;
use Closure;
use Illuminate\Http\Request;

class TrackUserActivity
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
        date_default_timezone_set("Asia/Karachi");
        $response = $next($request);

        if (auth()->check()) {
            UserActivity::create([
                'user_id' => auth()->user()->id,
                'activity_type' => $request->method(),
                'description' => $request->fullUrl(),
                'date' => date('Y-m-d')
            ]);
        }

        return $response;
    }
}
