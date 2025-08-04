<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BoardJob extends Model
{
    use SoftDeletes;
    
    //
    protected $table = 'board_jobs';

    // Columns
    protected $fillable = [
        'board_id',
        'job_id',
        'is_featured',
        'is_active',
    ];

    public function board()
    {
        return $this->belongsTo(Board::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
