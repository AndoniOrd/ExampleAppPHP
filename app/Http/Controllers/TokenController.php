<?php
namespace App\Http\Controllers;

use App\Models\CustomToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    /**
     * Generate a custom token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateToken(Request $request)
    {
        // Generate a random plain-text token
        $plainTextToken = Str::random(40);

        // Hash the token before storing it in the database
        $hashedToken = hash('sha256', $plainTextToken);

        // Save the token to the custom_tokens table
        $token = CustomToken::create([
            'name' => 'api-token', // You can customize this name
            'token' => $hashedToken,
            'abilities' => ['*'], // Wildcard for all abilities
        ]);

        // Return the plain-text token to the client
        return response()->json([
            'token' => $plainTextToken,
        ], 201);
    }

    /**
     * Validate a custom token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
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

        // Token is valid
        return response()->json([
            'message' => 'Token is valid',
            'token' => $customToken,
        ]);
    }
}