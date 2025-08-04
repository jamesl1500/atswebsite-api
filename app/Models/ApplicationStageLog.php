<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationStageLog extends Model
{
    use SoftDeletes;
    
    //
    protected $table = "application_stage_logs";

    // Columns
    protected $fillable = [
        'application_id',
        'pipeline_stage_id',
        'user_id',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function pipelineStage()
    {
        return $this->belongsTo(PipelineStage::class, 'pipeline_stage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
