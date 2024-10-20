<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(empty($request->bearerToken())){
            return response()->json([
                'success' => false,
                'api_key' => '',
                'message' => 'Bearer token required!',
                'data'    => (object)[]
            ], 401);
        }

        $user = User::where('token','=',$request->bearerToken())->first();

        if(empty($user)){
            return response()->json([
                'success' => false,
                'message' => 'User not found!',
                'data'    => (object)[]
            ], 401);
        }

        $request->attributes->add(['auth' => $user]);
        
        return $next($request);
    }
}
