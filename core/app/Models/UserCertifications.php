<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCertifications extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'course_name', 'completion_date'];

    // Define relationships if needed
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
