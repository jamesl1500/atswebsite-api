<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    //
    protected $table = 'files';

    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
        'extension',
    ];
}
