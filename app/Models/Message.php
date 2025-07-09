<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    //
    protected $table = 'messages';

    protected $fillable = [
        'conversation_id',
        'user_id',
        'content',
        'type',
        'attachment_id',
        'is_read',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachment()
    {
        return $this->belongsTo(File::class, 'attachment_id');
    }
}
