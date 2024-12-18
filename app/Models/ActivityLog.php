<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['action', 'description', 'icon', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
