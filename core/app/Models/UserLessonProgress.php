<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLessonProgress extends Model
{
    protected $fillable = [
        'user_id',
        'lesson_id',
        'pause_time',
        'lesson_status',

    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Models\Lesson');
    }
}
