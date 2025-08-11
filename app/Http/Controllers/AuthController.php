<?php

namespace App\Http\Controllers;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Libraries\UserLibrary;

use Laravel\Sanctum\PersonalAccessToken;

use Illuminate\Auth\Events\Registered;

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
     * Forgot Password
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
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

        // Send the password reset link
        $user->sendPasswordResetNotification();

        // Return success response
        return response()->json(['message' => 'Password reset link sent successfully']);
    }

    /**
     * Reset Password
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Get the user by email
        $user = $this->userLibrary->getUserByEmail($validatedData['email']);

        // Check if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Update the user's password
        $user->password = Hash::make($validatedData['password']);
        $user->save();

        // Return success response
        return response()->json(['message' => 'Password reset successfully']);
    }

    /**
     * Get authenticated user.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAuthenticatedUser(Request $request)
    {
        // Get the authenticated user
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated 1'], 401);
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
     * Verify user email.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(Request $request, $id, $hash)
    {
        // Find the user by ID
        $user = $this->userLibrary->getUserById($id);

        // Check if user exists
        if (!$user) {
            return redirect('/')->with('error', 'User not found');
        }

        // Verify the email
        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect('/')->with('error', 'Invalid verification link');
        }

        // Mark the email as verified
        $user->markEmailAsVerified();

        // Generate a new token for the user
        $token = $user->createToken('email-verification')->plainTextToken;

        // Lets see what the current onboarding stage is
        if ($user->is_onboarding) {
            // Redirect to the onboarding page
            return redirect(env('FRONTEND_URL') . '/onboarding/' . $user->onboarding_stage . '?token=' . $token)
                ->with('success', 'Email verified successfully, please complete the onboarding process');
        }

        // Redirect to the home page with success message
        return redirect(env('FRONTEND_URL') . '/')->with('success', 'Email verified successfully');
    }

    /**
     * Consume a token.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function consumeToken(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'token' => 'required|string',
        ]);

        // Find token
        $token = PersonalAccessToken::findToken($validatedData['token']);

        // Check if token exists
        if (!$token) {
            return response()->json(['message' => 'Token not found'], 404);
        }

        // Verify user
        $user = $token->tokenable;

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Login the user via session
        Auth::login($user);

        // Get a new user token
        $user->createToken('Personal Access Token')->plainTextToken;

        // Revoke token
        $token->delete();

        // Return success response
        return response()->json(['message' => 'Token consumed successfully, user logged in', 'user' => $user]);
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

        // Fire the Registered event
        event(new Registered($createUser));

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
