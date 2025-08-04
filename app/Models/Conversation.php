<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use SoftDeletes;

    //
    protected $table = 'conversations';

    protected $fillable = [
        'user_id',
        'title',
    ];
}
