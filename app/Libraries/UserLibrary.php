<?php

namespace App\Libraries;

use App\Models\User;

use App\Libraries\FileLibrary;

class UserLibrary
{
    /**
     * Supported user roles.
     * 
     * @var array<string>
     */
    protected $supportedUserRoles = [
        'admin',
        'user',
        'recruiter',
    ];

    /**
     * FileLibrary instance.
     * 
     * @var \App\Libraries\FileLibrary
     */
    protected $fileLibrary;
    
    /**
     * UserLibrary constructor.
     */
    public function __construct()
    {
        $this->fileLibrary = new FileLibrary();
    }

    /**
     * Get user by ID.
     *
     * @param int $id
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        return User::find($id);
    }

    /**
     * Get user by email.
     *
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get user by username.
     * 
     * @param string $username
     * @return User|null
     */
    public function getUserByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        // Check for existing user with same email or username
        if (User::where('email', $data['email'])->exists() || User::where('username', $data['username'])->exists()) {
            throw new \Exception('User with this email or username already exists.');
        }

        // Handle profile_picture and cover_picture uploads
        if (!empty($data['profile_picture'])) {
            $file = $this->fileLibrary->upload($data['profile_picture']);

            if (!$file) {
                throw new \Exception('Failed to upload profile picture.');
            }

            // Set profile_picture_id to the uploaded file's ID
            $data['profile_picture_id'] = $file->id;
            unset($data['profile_picture']);
        }

        if (!empty($data['cover_picture'])) {
            $file = $this->fileLibrary->upload($data['cover_picture']);

            if (!$file) {
                throw new \Exception('Failed to upload cover picture.');
            }

            // Set cover_picture_id to the uploaded file's ID
            $data['cover_picture_id'] = $file->id;
            unset($data['cover_picture']);
        }

        // Create and return the user model instance
        $user = User::create($data);

        // Make sure $user succeeded
        if (!$user) {
            throw new \Exception('Failed to create user.');
        }

        return $user;
    }

    /**
     * Update user by ID.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateUser(int $id, array $data): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->update($data);
        }
        return false;
    }

    /**
     * Delete user by ID.
     *
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $user = User::find($id);
        if ($user) {
            return $user->delete();
        }
        return false;
    }

    /**
     * Get all users.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers()
    {
        return User::all();
    }
}