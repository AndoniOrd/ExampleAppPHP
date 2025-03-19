<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 * )
 */
class UserApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="Authenticate user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email_address", "password"},
     *             @OA\Property(property="email_address", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", example="securepassword")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Login successful", @OA\JsonContent(
     *         @OA\Property(property="token", type="string", example="your-api-token")
     *     )),
     *     @OA\Response(response=401, description="Invalid credentials")
     * )
     */
    public function login(Request $request)
    {
        $request->validate([
            'email_address' => 'required|email',
            'password' => 'required',
        ]);
    
        if (!Auth::attempt(['email_address' => $request->email_address, 'password' => $request->password])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    
        return response()->json([
            'data' => [
                'token' => $request->user()->createToken('api')->plainTextToken,
            ]
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/users",
     *     summary="Get list of users",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="List retrieved successfully")
     * )
     */
    public function index()
    {       
        $users = User::all();
        return response()->json($users);
    }

  /**
 * @OA\Post(
 *     path="/api/users",
 *     summary="Create a user",
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email_address", "password"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email_address", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="password")
 *         )
 *     ),
 *     @OA\Response(response=201, description="User created successfully")
 * )
 */
public function store(Request $request)
{

    $request->validate([
        'name' => 'required|string|max:255',
        'email_address' => 'required|email|unique:users,email_address',
        'password' => 'required|min:6',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email_address' => $request->email_address,
        'password' => Hash::make($request->password),
    ]);

    return response()->json(['data' => new UserResource($user)], 201);
}

    /**
     * @OA\Get(
     *     path="/api/users/{id}",
     *     summary="Get a specific user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="User retrieved successfully")
     * )
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

/**
 * @OA\Put(
 *     path="/api/users/{id}",
 *     summary="Update a user",
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="User ID",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="first_name", type="string", example="John"),
 *             @OA\Property(property="last_name", type="string", example="Doe"),
 *             @OA\Property(property="email_address", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="phone_number", type="string", example="123-456-7890"),
 *             @OA\Property(property="account_status", type="string", example="active"),
 *             @OA\Property(property="company_name", type="string", example="Acme Corp"),
 *             @OA\Property(property="company_address", type="string", example="123 Main St"),
 *             @OA\Property(property="vat_tax_id", type="string", example="TAX12345"),
 *             @OA\Property(property="industry", type="string", example="Technology"),
 *             @OA\Property(property="company_size", type="integer", example=100),
 *             @OA\Property(property="website", type="string", format="url", example="https://example.com")
 *         )
 *     ),
 *     @OA\Response(response=200, description="User updated successfully")
 * )
 */
public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'first_name' => 'sometimes|string|max:255',
        'last_name' => 'sometimes|string|max:255',
        'email_address' => "sometimes|email|unique:users,email_address,{$id}",
        'phone_number' => 'sometimes|string|max:20',
        'account_status' => 'sometimes|string|in:active,inactive,suspended',
        'company_name' => 'sometimes|string|max:255',
        'company_address' => 'sometimes|string',
        'vat_tax_id' => 'sometimes|string|max:255',
        'industry' => 'sometimes|string|max:255',
        'company_size' => 'sometimes|integer',
        'website' => 'sometimes|url|max:255',
    ]);

    $user->update($validated);

    return response()->json($user);
}
    /**
     * @OA\Delete(
     *     path="/api/users/{id}",
     *     summary="Delete a user",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="User deleted successfully")
     * )
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        $user->delete();
        return response()->json(null, 204);
    }
}