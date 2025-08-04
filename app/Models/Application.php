<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;
    
    // The table associated with the model.
    protected $table = 'applications';

    // The attributes that are mass assignable.
    protected $fillable = [
        'job_id',
        'name',
        'email',
        'phone',
        'cover_letter',
        'resume',
        'current_stage_id',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function resume()
    {
        return $this->belongsTo(File::class, 'resume');
    }

    public function currentStage()
    {
        return $this->belongsTo(PipelineStage::class, 'current_stage_id');
    }
}
