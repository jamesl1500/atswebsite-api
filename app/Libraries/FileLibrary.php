<?php

namespace App\Libraries;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileLibrary
{
    /**
     * Supported file types.
     * 
     * @var array<string>
     */
    protected $supportedFileTypes = [
        'image/jpeg',
        'image/png',
        'application/pdf',
        'application/msword',
    ];

    /**
     * Supported extensions.
     * 
     * @var array<string>
     */
    protected $supportedFileExtensions = [
        'jpg',
        'jpeg',
        'png',
        'pdf',
        'doc',
        'docx',
    ];

    /**
     * Get file by ID.
     * 
     * @param int $fileId
     * @return File|null
     */
    public function getFileById($fileId)
    {
        return File::find($fileId);
    }

    /**
     * Get all files.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllFiles()
    {
        return File::all();
    }

    /**
     * Get file type by file ID.
     * 
     * @param int $fileId
     * @return string|null
     */
    public function getFileTypeById($fileId)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        // Return the MIME type of the file
        return $file->type;
    }

    /**
     * Get file name by file ID.
     * 
     * @param int $fileId
     * @return string|null
     */
    public function getFileNameById($fileId)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        // Return the name of the file
        return $file->name;
    }

    /**
     * Get file path by file ID.
     * 
     * @param int $fileId
     * @return string|null
     */
    public function getFilePathById($fileId)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        // Return the path of the file
        return $file->path;
    }

    /**
     * Get file size by file ID.
     * 
     * @param int $fileId
     * @return int|null
     */
    public function getFileSizeById($fileId)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        // Return the size of the file
        return $file->size;
    }

    /**
     * Get file extension by file ID.
     * 
     * @param int $fileId
     * @return string|null
     */
    public function getFileExtensionById($fileId)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        // Return the extension of the file
        return $file->extension;
    }

    /**
     * Upload a file and create a File model record.
     *
     * @param \Illuminate\Http\UploadedFile $uploadedFile
     * @param string $directory
     * @return File
     */
    public function upload($uploadedFile, $directory = 'files')
    {
        // Validate that $uploadedFile is an instance of UploadedFile
        if (!$uploadedFile || !($uploadedFile instanceof \Illuminate\Http\UploadedFile)) {
            return array('error' => 'Invalid file upload.');
        }

        // Validate MIME type
        $mimeType = $uploadedFile->getClientMimeType();
        if (!in_array($mimeType, $this->supportedFileTypes)) {
            return array('error' => 'Unsupported file type.');
        }

        // Validate extension
        $extension = strtolower($uploadedFile->getClientOriginalExtension());
        if (!in_array($extension, $this->supportedFileExtensions)) {
            return array('error' => 'Unsupported file extension.');
        }

        // Attempt to upload the file
        $path = $uploadedFile->store($directory);
        if (!$path) {
            return array('error' => 'File upload failed.');
        }

        // Create and save the File model
        $file = new File();
        $file->name = $uploadedFile->getClientOriginalName();
        $file->path = $path;
        $file->type = $mimeType;
        $file->size = $uploadedFile->getSize();
        $file->extension = $extension;
        $file->save();

        return $file;
    }

    /**
     * Delete a file from storage and remove its record.
     *
     * @param int $fileId
     * @return bool
     */
    public function delete($fileId)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return false;
        }

        // Delete the file from storage
        Storage::delete($file->path);

        // Delete the file record from the database
        $file->delete();

        return true;
    }

    /**
     * Get the URL to access the file.
     *
     * @param int $fileId
     * @return string|null
     */
    public function getUrl($fileId)
    {
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        return Storage::url($file->path);
    }

    /**
     * Download file by ID.
     * 
     * @param int $fileId
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|null
     */
    public function download($fileId)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        // Check if the file exists in storage
        return Storage::download($file->path, $file->name);
    }

    /**
     * Get file info.
     *
     * @param int $fileId
     * @return File|null
     */
    public function getFile($fileId)
    {
        return File::find($fileId);
    }

    /**
     * Update file
     * 
     * @param int $fileId
     * @param array $data
     * @return File|null
     */
    public function updateFile($fileId, array $data)
    {
        // Find the file record by ID
        $file = File::find($fileId);

        if (!$file) {
            return null;
        }

        // Update the file attributes
        $file->name = $data['name'] ?? $file->name;
        $file->path = $data['path'] ?? $file->path;
        $file->type = $data['type'] ?? $file->type;
        $file->size = $data['size'] ?? $file->size;
        $file->extension = $data['extension'] ?? $file->extension;

        // Save the updated file record
        $file->save();

        return $file;
    }
}