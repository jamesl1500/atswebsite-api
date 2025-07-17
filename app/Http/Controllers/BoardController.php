<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Libraries\BoardLibrary;
use App\Models\Board;

class BoardController extends Controller
{
    /**
     * The board library instance.
     *
     * @var BoardLibrary
     */
    protected $boardLibrary;

    /**
     * Create board model instance.
     * 
     * @var BoardLibrary $boardLibrary
     */
    protected $boardModel;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->boardLibrary = new BoardLibrary();
        $this->boardModel = new Board();
    }

    /**
     * Get all boards.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $boards = $this->boardLibrary->getAllBoards();
        return response()->json($boards);
    }

    /**
     * Get boards by user ID.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBoardsByUserId($userId)
    {
        $boards = $this->boardLibrary->getBoardsByUserId($userId);
        return response()->json($boards);
    }

    /**
     * Get boards by company ID.
     *
     * @param int $companyId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getBoardsByCompanyId($companyId)
    {
        $boards = $this->boardLibrary->getBoardsByCompanyId($companyId);
        return response()->json($boards);
    }

    /**
     * Get a board by ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $board = $this->boardLibrary->getBoardById($id);
        if (!$board) {
            return response()->json(['message' => 'Board not found'], 404);
        }
        return response()->json($board);
    }

    /**
     * Get a board by slug.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBySlug($slug)
    {
        $board = $this->boardLibrary->getBoardBySlug($slug);
        if (!$board) {
            return response()->json(['message' => 'Board not found'], 404);
        }
        return response()->json($board);
    }

    /**
     * Create a new board.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'user_id' => 'required|integer|exists:users,id',
            'company_id' => 'required|integer|exists:companies,id',
            'description' => 'nullable|string|max:1000',
            'slug' => 'nullable|string|max:255|unique:boards,slug',
            'logo_id' => 'nullable|integer|exists:files,id',
            'cover_id' => 'nullable|integer|exists:files,id',
            'theme_color' => 'nullable|string|max:7',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the board using the board library
        $boardData = $request->only(['name', 'user_id', 'company_id', 'description', 'slug', 'logo_id', 'cover_id', 'theme_color', 'is_public']);
        $board = $this->boardLibrary->createBoard($boardData);

        if (!$board) {
            return response()->json(['message' => 'Failed to create board'], 500);
        }

        // Return the created board
        return response()->json($board, 201);
    }

    /**
     * Update an existing board.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Ensure the board exists
        $board = $this->boardLibrary->getBoardById($id);
        if (!$board) {
            return response()->json(['message' => 'Board not found'], 404);
        }

        // Check if the logged in user is the owner of the board
        if ($request->user()->id !== $board->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'company_id' => 'sometimes|required|integer|exists:companies,id',
            'description' => 'nullable|string|max:1000',
            'slug' => 'sometimes|nullable|string|max:255|unique:boards,slug,' . $id,
            'logo_id' => 'sometimes|nullable|integer|exists:files,id',
            'cover_id' => 'sometimes|nullable|integer|exists:files,id',
            'theme_color' => 'sometimes|nullable|string|max:7',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the board using the board library
        $boardData = $request->only(['name', 'user_id', 'company_id', 'description', 'slug', 'logo_id', 'cover_id', 'theme_color', 'is_public']);
        $updatedBoard = $this->boardLibrary->updateBoard($id, $boardData);

        if (!$updatedBoard) {
            return response()->json(['message' => 'Failed to update board or board not found'], 404);
        }

        // Return the updated board
        return response()->json($updatedBoard);
    }

    /**
     * Delete a board.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        // Ensure the board exists
        $board = $this->boardLibrary->getBoardById($id);

        if (!$board) {
            return response()->json(['message' => 'Board not found'], 404);
        }

        // Check if the logged in user is the owner of the board
        if ($request->user()->id !== $board->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete the board using the board library
        $deleted = $this->boardLibrary->deleteBoard($id);

        if (!$deleted) {
            return response()->json(['message' => 'Failed to delete board'], 500);
        }

        return response()->json(['message' => 'Board deleted successfully'], 200);
    }
}
