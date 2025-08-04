<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobCustomField extends Model
{
    use SoftDeletes;
    
    // Table Name
    protected $table = "job_custom_fields";

    // Fillable Columns
    protected $fillable = [
        'job_id',
        'label',
        'type',
        'options',
        'required',
        'order'
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
