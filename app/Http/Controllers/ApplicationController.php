<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Application;
use App\Models\File;

class ApplicationController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $applications = Application::all();
        return response()->json($applications);
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        // Incoming request validation
        $validatedData = $request->validate([
            'job_id' => 'required|exists:jobs,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:applications,email',
            'phone' => 'required|string|max:15',
            'cover_letter' => 'nullable|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Handle file upload for resume
        if ($request->hasFile('resume')){
            $file = $request->file('resume');
            $path = $file->store('resumes', 'public'); // Store in public disk
            
            // Add file into database
            $savedFile = File::create([
                'path' => $path,
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'extension' => $file->getMimeType(),
                'type' => $file->getClientOriginalExtension(),
            ]);

            $validatedData['resume'] = $savedFile->id; // Store the file ID in the application
        } else {
            return response()->json(['message' => 'Resume file is required'], 422);
        }

        // Add current stage ID
        $validatedData['current_stage_id'] = 1; // Assuming 1 is the ID for the initial stage

        $application = Application::create($validatedData);
        return response()->json($application, 201);
    }

    // Display the specified resource
    public function show($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        return response()->json($application);
    }

    // Show the form for editing the specified resource
    public function edit($id)
    {
        // Typically, this would return a view in a web application
        return response()->json(['message' => 'Edit form not applicable for API']);
    }

    // Update the specified resource in storage
    public function update(Request $request, $id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:applications,email,' . $id,
            'phone' => 'sometimes|required|string|max:15',
        ]);

        $application->update($validatedData);
        return response()->json($application);
    }

    // Remove the specified resource from storage
    public function destroy($id)
    {
        $application = Application::find($id);

        if (!$application) {
            return response()->json(['message' => 'Application not found'], 404);
        }

        $application->delete();
        return response()->json(['message' => 'Application deleted successfully']);
    }
}
