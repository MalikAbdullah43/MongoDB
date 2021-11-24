<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Service\ConnectionService;

class UserVerify
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $connect    = new ConnectionService();
        $database    = $connect->getdb();
        $collection = "users";
        $jwt = $request->bearerToken();
        $token =  $database->$collection->findOne([
            '$and' => [
                ['remember_token' => $jwt],
                ['updated_at' => ['$gte' => now()->__toString()]]
            ]
        ]);
        if (empty($token)) {
            return response(["Message" => "Unauthorized Access or Token Expire", "Status" => "404"], 404);
        } else {
            return $next($request);
        }
    }
}
