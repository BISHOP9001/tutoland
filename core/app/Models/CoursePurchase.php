<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class CoursePurchase extends Model
{
    use Searchable;

    public const PENDING = 0;
    public const APPROVED = 1;
    public const REJECTED = 2;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
