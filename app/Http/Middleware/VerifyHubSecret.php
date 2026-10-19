<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Institute -> Hub calls (register-institute, register-student) must send
 * the shared PARENT_HUB_SECRET as "secret_key".
 */
class VerifyHubSecret
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('services.parent_hub.secret');
        $given = (string) $request->input('secret_key', '');

        if ($expected === '' || $given === '' || !hash_equals($expected, $given)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
