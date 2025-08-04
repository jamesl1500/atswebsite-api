<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipelineStage extends Model
{
    use SoftDeletes;
    
    //
    protected $table = 'pipeline_stages';

    protected $fillable = [
        'job_id',
        'name',
        'description',
        'order',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }
}
