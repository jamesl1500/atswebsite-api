<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Libraries\FileLibrary;
use App\Models\File;

class FileController extends Controller
{
    /**
     * FileLibrary instance.
     * 
     * @var \App\Libraries\FileLibrary
     */
    protected $fileLibrary;

    /**
     * File model instance.
     * 
     * @var \App\Models\File
     */
    protected $fileModel;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->fileLibrary = new FileLibrary();
        $this->fileModel = new File();
    }

    /**
     * Get file by ID.
     * 
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileById($fileId)
    {
        // See if file exists
        $file = $this->fileLibrary->getFileById($fileId);

        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->json($file);
    }

    /**
     * Get all files.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $files = $this->fileLibrary->getAllFiles();
        return response()->json($files);
    }

    /**
     * Get file type by file ID.
     * 
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileTypeById($fileId)
    {
        // Get file type
        $fileType = $this->fileLibrary->getFileTypeById($fileId);

        if (!$fileType) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->json(['type' => $fileType]);
    }

    /**
     * Get file name by file ID.
     * 
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileNameById($fileId)
    {
        // Get file name
        $fileName = $this->fileLibrary->getFileNameById($fileId);
        if (!$fileName) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->json(['name' => $fileName]);
    }

    /**
     * Get file path by file ID.
     * 
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFilePathById($fileId)
    {
        // Get file path
        $filePath = $this->fileLibrary->getFilePathById($fileId);

        if (!$filePath) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->json(['path' => $filePath]);
    }

    /**
     * Get file size by file ID.
     * 
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileSizeById($fileId)
    {
        // Get file size
        $fileSize = $this->fileLibrary->getFileSizeById($fileId);

        if (!$fileSize) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->json(['size' => $fileSize]);
    }

    /**
     * Get file extension by file ID.
     * 
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFileExtensionById($fileId)
    {
        // Get file extension
        $fileExtension = $this->fileLibrary->getFileExtensionById($fileId);

        if (!$fileExtension) {
            return response()->json(['error' => 'File not found'], 404);
        }
        return response()->json(['extension' => $fileExtension]);
    }

    /** 
     * Create a new file.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'file' => 'required|file',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create file
        $file = $this->fileLibrary->upload($request->all());

        if (!$file) {
            return response()->json(['error' => 'File upload failed'], 500);
        }

        return response()->json($file, 201);
    }

    /**
     * Update an existing file.
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $fileId)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'file' => 'sometimes|file',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update file
        $file = $this->fileLibrary->updateFile($fileId, $request->all());

        if (!$file) {
            return response()->json(['error' => 'File update failed'], 500);
        }

        return response()->json($file);
    }

    /**
     * Delete a file.
     * 
     * @param int $fileId
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($fileId)
    {
        // Delete file
        $deleted = $this->fileLibrary->delete($fileId);

        if (!$deleted) {
            return response()->json(['error' => 'File deletion failed'], 500);
        }

        return response()->json(['message' => 'File deleted successfully'], 200);
    }

}