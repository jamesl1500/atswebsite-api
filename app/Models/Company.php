<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    //
    protected $table = 'companies';

    protected $fillable = [
        'owner_id',
        'name',
        'slug',
        'description',
        'website',
        'logo_id',
        'cover_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function logo()
    {
        return $this->belongsTo(File::class, 'logo_id');
    }

    public function cover()
    {
        return $this->belongsTo(File::class, 'cover_id');
    }
}
