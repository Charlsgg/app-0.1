<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // 1. Override the table name
    protected $table = 'table_users';

    // 2. Override the primary key
    protected $primaryKey = 'user_id';

    // 3. Tell Eloquent the primary key is NOT auto-incrementing
    public $incrementing = true;

    // 4. Disable standard timestamps (since you don't have updated_at)
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'user_type',
        'created_at',
        'password', 
        'updated_at',
    ];

    // Define the One-to-Many relationship with Announcements
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'author_id', 'user_id');
    }
}