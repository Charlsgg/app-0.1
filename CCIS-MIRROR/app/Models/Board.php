<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    protected $table = 'table_board';
    
    protected $primaryKey = 'board_id';
    
    public $incrementing = false;
    
    public $timestamps = false;

    protected $fillable = [
        'board_id',
        'board_name',
        'created_at',
        'updated_at',
    ];

    // Define the One-to-Many relationship with Announcements
    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'board_id', 'board_id');
    }
}