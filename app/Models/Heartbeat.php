<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Heartbeat extends Model
{
    // The table associated with the model.
    protected $table = 'heartbeats';

    // The attributes that are mass assignable.
    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'is_authenticated',
    ];

    /**
     * Get the user that owns the heartbeat.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the heartbeat is from an authenticated user.
     */
    public function isAuthenticated()
    {
        return $this->is_authenticated;
    }
}
