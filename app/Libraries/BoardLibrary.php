<?php

namespace App\Libraries;

use App\Models\Board;

class BoardLibrary
{
    /**
     * Get all boards.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllBoards()
    {
        return Board::all();
    }

    /**
     * Get boards by user ID.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBoardsByUserId($userId)
    {
        return Board::where('user_id', $userId)->get();
    }

    /**
     * Get boards by company ID.
     *
     * @param int $companyId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBoardsByCompanyId($companyId)
    {
        return Board::where('company_id', $companyId)->get();
    }

    /**
     * Find a board by ID.
     *
     * @param int $id
     * @return Board|null
     */
    public function getBoardById($id)
    {
        return Board::find($id);
    }

    /** 
     * Find board by slug
     * 
     * @param string $slug
     * @return Board|null
     */
    public function getBoardBySlug($slug)
    {
        return Board::where('slug', $slug)->first();
    }

    /**
     * Create a new board.
     *
     * @param array $data
     * @return Board
     */
    public function createBoard(array $data)
    {
        return Board::create($data);
    }

    /**
     * Update an existing board.
     *
     * @param int $id
     * @param array $data
     * @return Board|null
     */
    public function updateBoard($id, array $data)
    {
        $board = Board::find($id);
        if ($board) {
            $board->update($data);
        }
        return $board;
    }

    /**
     * Delete a board.
     *
     * @param int $id
     * @return bool
     */
    public function deleteBoard($id)
    {
        $board = Board::find($id);
        if ($board) {
            return $board->delete();
        }
        return false;
    }
}