<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;


    protected $fillable = ['user_id', 'name', 'description', 'status','tags'];

    protected $casts = [
        'tags' => 'array',
    ];
    
    // Each task belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
