<?php

namespace App\Http\Controllers\API;

use App\Enums\Auth\TokenName;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Handle the user login request
     *
     * @param \Illuminate\Http\Request $request The HTTP request object
     * @return \Illuminate\Http\JsonResponse The JSON response containing the user data and access token
     */
    public function handleLogin(Request $request)
    {
        // Validate the request data
        $input = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        // Attempt to authenticate the user
        if (!Auth::attempt($input)) {
            // If authentication fails, return an error response
            $message = 'The provided credentials do not match our records.';
            $errors = ['email' => $message];
            return response()->json([
                'message' => $message,
                'errors' => $errors,
            ])->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Create a new API token for the user
        $token = $request->user()->createToken(TokenName::APIToken);

        // Return the JSON response
        return response()->json([
            'message' => 'Login successful.',
            'user' => array_merge(
                $request->user()->toArray(),
                ['token' => $token->plainTextToken]
            )
        ]);
    }

    /**
     * Logout the user
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleLogout(Request $request)
    {
        // Delete the current access token
        $request->user()->tokens()->where('name', TokenName::APIToken)->delete();

        // Return a success message
        return response()->json([
            'message' => 'Successfully logged out.'
        ]);
    }
}
