<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
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
