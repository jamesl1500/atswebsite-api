<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

use App\Libraries\UserLibrary;

class AuthController extends Controller
{
    /**
     * User Library instance.
     * 
     * @var \App\Libraries\UserLibrary
     */
    protected $userLibrary;

    /**
     * User model instance.
     * 
     * @var \App\Models\User
     */
    protected $userModel;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->userLibrary = new UserLibrary();
        $this->userModel = new User();
    }

    /**
     * Get authenticated user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Return the authenticated user's information
        return response()->json($user);
    }

    /**
     * Resend verification email.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // Get the user by email
        $user = $this->userLibrary->getUserByEmail($validatedData['email']);

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Send the verification email
        $user->sendEmailVerificationNotification();

        // Return success response
        return response()->json(['message' => 'Verification email resent successfully']);
    }

    /**
     * Register a new user.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Run the create user logic
        $createUser = $this->userLibrary->createUser([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        if (!$createUser) {
            return response()->json(['message' => 'Failed to create user'], 500);
        }

        // Generate a token for the user
        $token = $createUser->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'user' => $createUser,
            'token' => $token,
            'message' => 'Registration successful, please verify your email.',
        ], 201);
    }

    public function login(Request $request)
    {
        // Validate the request data
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Attempt to authenticate the user
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a token for the authenticated user
        $user = auth()->user();
        $token = $user->createToken('Personal Access Token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'User logged in successfully',
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke the user's token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'User logged out successfully']);
    }

    public function logoutAll(Request $request)
    {
        // Revoke all tokens for the authenticated user
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'All user tokens revoked successfully']);
    }

    public function user(Request $request)
    {
        // Return the authenticated user's information
        return response()->json($request->user());
    }
}
