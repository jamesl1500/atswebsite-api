<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Heartbeat;

class HeartbeatController extends Controller
{
    /**
     * The Heartbeat model instance.
     *
     * @var Heartbeat
     */
    protected $heartbeatModel;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->heartbeatModel = new Heartbeat();
    }

    /**
     * Get all heartbeats.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Return all heartbeats
        return response()->json($this->heartbeatModel->all());
    }

    /**
     * Create a new heartbeat.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string',
            'is_authenticated' => 'boolean',
        ]);

        // Create a new heartbeat
        $heartbeat = $this->heartbeatModel->create($validatedData);

        return response()->json($heartbeat, 201);
    }

    /**
     * Show heartbeat data.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $heartbeat = $this->heartbeatModel->find($id);

        if (!$heartbeat) {
            return response()->json(['message' => 'Heartbeat not found'], 404);
        }

        return response()->json($heartbeat, 200);
    }

    /**
     * Update the specified heartbeat.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'ip_address' => 'nullable|ip',
            'user_agent' => 'nullable|string',
            'is_authenticated' => 'boolean',
        ]);

        $heartbeat = $this->heartbeatModel->find($id);

        if (!$heartbeat) {
            return response()->json(['message' => 'Heartbeat not found'], 404);
        }

        $heartbeat->update($validatedData);

        return response()->json($heartbeat, 200);
    }

    /**
     * Delete the specified heartbeat.
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $heartbeat = $this->heartbeatModel->find($id);

        if (!$heartbeat) {
            return response()->json(['message' => 'Heartbeat not found'], 404);
        }

        $heartbeat->delete();

        return response()->json(['message' => 'Heartbeat deleted successfully'], 200);
    }
}
