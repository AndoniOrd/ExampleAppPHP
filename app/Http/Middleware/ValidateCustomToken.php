<?php

namespace App\Http\Middleware;

use App\Models\CustomToken;
use Closure;
use Illuminate\Http\Request;

class ValidateCustomToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Extract the token from the Authorization header
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token missing'], 401);
        }

        // Hash the token to compare with the stored hash
        $hashedToken = hash('sha256', $token);

        // Check if the token exists in the database
        $customToken = CustomToken::where('token', $hashedToken)->first();

        if (!$customToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        // Attach the token to the request for later use
        $request->attributes->set('custom_token', $customToken);

        return $next($request);
    }
}