<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationAnswer extends Model
{
    use SoftDeletes;
    
    //
    protected $table = 'application_answers';

    protected $fillable = [
        'application_id',
        'job_custom_field_id',
        'answer',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function jobCustomField()
    {
        return $this->belongsTo(JobCustomField::class, 'job_custom_field_id');
    }

    public function answer()
    {
        return $this->answer;
    }

}
