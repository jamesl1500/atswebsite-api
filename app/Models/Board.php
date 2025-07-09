<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    //
    protected $table = 'boards';

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'description',
        'slug',
        'logo_id',
        'cover_id',
        'theme_color',
        'is_public',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
