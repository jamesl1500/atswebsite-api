<?php
namespace App\Libraries;

use App\Models\Application;

class ApplicationLibrary
{
    /**
     * Get all applications.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllApplications()
    {
        return Application::all();
    }

    /**
     * Find an application by ID.
     *
     * @param int $id
     * @return Application|null
     */
    public function findApplicationById($id)
    {
        return Application::find($id);
    }

    /**
     * Create a new application.
     *
     * @param array $data
     * @return Application
     */
    public function createApplication(array $data)
    {
        return Application::create($data);
    }

    /**
     * Update an existing application.
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateApplication($id, array $data)
    {
        $application = Application::find($id);
        if ($application) {
            return $application->update($data);
        }
        return false;
    }

    /**
     * See if application is trashed
     * 
     * @param int $id
     * @return bool
     */
    public function isApplicationTrashed($id)
    {
        $application = Application::withTrashed()->find($id);
        return $application ? $application->trashed() : false;
    }

    /**
     * Delete an application.
     *
     * @param int $id
     * @return bool|null
     */
    public function deleteApplication($id)
    {
        $application = Application::find($id);
        if ($application) {
            return $application->forceDelete();
        }
        return false;
    }

    /**
     * Restore a soft-deleted application.
     * 
     * @param int $id
     * @return bool|null
     */
    public function restoreApplication($id)
    {
        $application = Application::withTrashed()->find($id);

        if ($application) {
            return $application->restore();
        }
        return false;
    }

    /**
     * Soft delete an application.
     *
     * @param int $id
     * @return bool|null
     */
    public function softDeleteApplication($id)
    {
        $application = Application::find($id);
        if ($application) {
            return $application->delete();
        }
        return false;
    }
}