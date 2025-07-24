<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use App\Libraries\UserLibrary;
use App\Libraries\FileLibrary;
use App\Libraries\CompanyLibrary;

class OnboardingController extends Controller
{
    /**
     * UserLibrary instance.
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
     * FileLibrary instance.
     * 
     * @var \App\Libraries\FileLibrary
     */
    protected $fileLibrary;

    /**
     * CompanyLibrary instance.
     * 
     * @var \App\Libraries\CompanyLibrary
     */
    protected $companyLibrary;

    /**
     * OnboardingController constructor.
     */
    public function __construct()
    {
        $this->userLibrary = new UserLibrary();
        $this->userModel = new User();
        $this->companyLibrary = new CompanyLibrary();
        $this->fileLibrary = new FileLibrary();
    }

    /**
     * Onboarding stages
     * @var array
     */
    protected $stages = [
        'welcome',
        'profile',
        'company',
        'complete',
    ];

    /**
     * Get the current onboarding stage for the user.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentStage(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'onboarding_stage' => $user->onboarding_stage,
        ]);
    }

    /**
     * Get the current onboarding status for the user.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOnboardingStatus(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'is_onboarding' => $user->is_onboarding,
        ]);
    }

    /**
     * Update the onboarding stage for the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOnboardingStage(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stage = $request->input('stage');

        if (!in_array($stage, $this->stages)) {
            return response()->json(['error' => 'Invalid onboarding stage'], 400);
        }

        // Update the user's onboarding stage
        $update = $this->userLibrary->setUserOnboardingStage($user->id, $stage);
        
        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding stage'], 500);
        }

        return response()->json([
            'message' => 'Onboarding stage updated successfully',
            'onboarding_stage' => $stage,
        ]);
    }

    /**
     * Update the onboarding status for the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOnboardingStatus(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $isOnboarding = $request->input('is_onboarding', true);

        // Update the user's onboarding status
        $update = $this->userLibrary->setUserOnboardingStatus($user->id, $isOnboarding);
        
        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding status'], 500);
        }

        return response()->json([
            'message' => 'Onboarding status updated successfully',
            'is_onboarding' => $isOnboarding,
        ]);
    }

    /**
     * Onboarding index method.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, $stage = 'welcome')
    {
        if (!in_array($stage, $this->stages)) {
            return response()->json(['error' => 'Invalid onboarding stage'], 400);
        }

        // See if there is a logged user 
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check to see if the user is already onboarded
        if (!$user->is_onboarding) {
            return response()->json(['message' => 'User is already onboarded'], 200);
        }

        // Now determine the current stage
        $currentStage = $user->onboarding_stage;
        
        // If the current stage is not the requested stage, return an error
        if ($currentStage !== $stage) {
            return response()->json(['error' => 'User is not at the requested onboarding stage'], 400);
        }

        // Return the current onboarding stage with the users data
        return response()->json([
            'onboarding_stage' => $currentStage,
            'is_onboarding' => $user->is_onboarding,
            'user' => $user,
        ]);
    }

    /**
     * Handle the "welcome" onboarding stage after the user presses the "Get Started" button.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function welcome(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the user is already onboarded
        if (!$user->is_onboarding) {
            return response()->json(['message' => 'User is already onboarded'], 200);
        }
        
        // Update the user's onboarding stage to the next stage
        $nextStage = 'profile';
        $update = $this->userLibrary->setUserOnboardingStage($user->id, $nextStage);

        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding stage'], 500);
        }
        
        return response()->json([
            'message' => 'Welcome to the onboarding process!',
            'onboarding_stage' => $nextStage,
            'is_onboarding' => true,
            'user' => $user,
        ], 200);
    }

    /**
     * Handle the "profile" onboarding stage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the user is already onboarded
        if (!$user->is_onboarding) {
            return response()->json(['message' => 'User is already onboarded'], 200);
        }

        // Check if the user is at the "profile" stage
        if ($user->onboarding_stage !== 'profile') {
            return response()->json(['error' => 'User is not at the profile onboarding stage'], 400);
        }

        // Lets see if the user uploaded a profile picture (required) and a cover picture (optional) in the request
        $validatedData = $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Process the profile picture
        $profilePicture = $this->userLibrary->updateUserProfilePicture($user->id, $validatedData['profile_picture']);

        if (!$profilePicture) {
            return response()->json(['error' => 'Failed to update profile picture'], 500);
        }

        // Process the cover picture if provided
        if (isset($validatedData['cover_picture'])) {
            $coverPicture = $this->userLibrary->updateUserCoverPicture($user->id, $validatedData['cover_picture']);

            if (!$coverPicture) {
                return response()->json(['error' => 'Failed to update cover picture'], 500);
            }
        }

        // Update the user's onboarding stage to the next stage
        $nextStage = 'company';
        $update = $this->userLibrary->setUserOnboardingStage($user->id, $nextStage);

        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding stage'], 500);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'onboarding_stage' => $nextStage,
            'is_onboarding' => true,
            'user' => $user,
        ], 200);
    }

    /**
     * Handle the "company" onboarding stage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function company(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the user is already onboarded
        if (!$user->is_onboarding) {
            return response()->json(['message' => 'User is already onboarded'], 200);
        }
        
        // Check if the user is at the "company" stage
        if ($user->onboarding_stage !== 'company') {
            return response()->json(['error' => 'User is not at the company onboarding stage'], 400);
        }

        // Validate the company data
        $validatedData = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_slug' => 'required|string|max:255|unique:companies,slug',
            'company_website' => 'required|url|max:255',
            'company_description' => 'nullable|string|max:1000',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Add logged user as the owner of the company
        $validatedData['owner_id'] = $user->id;

        // Create the company record
        $company = $this->companyLibrary->createCompany($validatedData);

        if (!$company) {
            return response()->json(['error' => 'Failed to create company'], 500);
        }

        // Update the user's onboarding stage to the next stage
        $nextStage = 'complete';
        $update = $this->userLibrary->setUserOnboardingStage($user->id, $nextStage);

        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding stage'], 500);
        }

        return response()->json([
            'message' => 'Company created successfully',
            'company' => $company,
        ], 201);
    }

    /**
     * Handle the "complete" onboarding stage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if the user is already onboarded
        if (!$user->is_onboarding) {
            return response()->json(['message' => 'User is already onboarded'], 200);
        }

        // Check if the user is at the "complete" stage
        if ($user->onboarding_stage !== 'complete') {
            return response()->json(['error' => 'User is not at the complete onboarding stage'], 400);
        }

        // Mark the user as fully onboarded
        $update = $this->userLibrary->setUserOnboardingStatus($user->id, false);

        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding status'], 500);
        }

        return response()->json([
            'message' => 'Onboarding process completed successfully',
            'is_onboarding' => false,
            'user' => $user,
        ], 200);
    }

    /**
     * Get the next onboarding stage for the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNextStage(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentStageIndex = array_search($user->onboarding_stage, $this->stages);
        
        if ($currentStageIndex === false || $currentStageIndex >= count($this->stages) - 1) {
            return response()->json(['error' => 'No next stage available'], 400);
        }

        $nextStage = $this->stages[$currentStageIndex + 1];

        return response()->json([
            'next_stage' => $nextStage,
        ]);
    }

    /**
     * Get the previous onboarding stage for the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPreviousStage(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentStageIndex = array_search($user->onboarding_stage, $this->stages);
        
        if ($currentStageIndex === false || $currentStageIndex <= 0) {
            return response()->json(['error' => 'No previous stage available'], 400);
        }

        $previousStage = $this->stages[$currentStageIndex - 1];

        return response()->json([
            'previous_stage' => $previousStage,
        ]);
    }
    
    /**
     * Move the user to the next onboarding stage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveToNextStage(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentStageIndex = array_search($user->onboarding_stage, $this->stages);
        
        if ($currentStageIndex === false || $currentStageIndex >= count($this->stages) - 1) {
            return response()->json(['error' => 'No next stage available'], 400);
        }

        $nextStage = $this->stages[$currentStageIndex + 1];

        // Update the user's onboarding stage
        $update = $this->userLibrary->setUserOnboardingStage($user->id, $nextStage);
        
        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding stage'], 500);
        }

        return response()->json([
            'message' => 'Moved to next onboarding stage successfully',
            'next_stage' => $nextStage,
        ]);
    }

    /**
     * Move the user to the previous onboarding stage.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveToPreviousStage(Request $request)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $currentStageIndex = array_search($user->onboarding_stage, $this->stages);

        if ($currentStageIndex === false || $currentStageIndex <= 0) {
            return response()->json(['error' => 'No previous stage available'], 400);
        }
        
        $previousStage = $this->stages[$currentStageIndex - 1];

        // Update the user's onboarding stage
        $update = $this->userLibrary->setUserOnboardingStage($user->id, $previousStage);
        
        if (!$update) {
            return response()->json(['error' => 'Failed to update onboarding stage'], 500);
        }

        return response()->json([
            'message' => 'Moved to previous onboarding stage successfully',
            'previous_stage' => $previousStage,
        ]);
    }
}
