<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $table = 'table_announcement';
    
    protected $primaryKey = 'announcement_id';
    
    public $incrementing = false;
    
    protected $fillable = [
        'title', 
        'board_id',
        'author_id',
        'content',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
    // Define the Inverse One-to-Many relationship to User
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    // Define the Inverse One-to-Many relationship to Board
    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id', 'board_id');
    }
}