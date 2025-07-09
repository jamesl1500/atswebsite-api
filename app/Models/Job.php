<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    // Table name
    protected $table = 'job';

    // Fillable attributes
    protected $fillable = [
        'user_id',
        'team_id',
        'company_id',
        'title',
        'company',
        'location',
        'description',
        'type',
        'salary',
        'remote',
        'published',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class);
    }
}
