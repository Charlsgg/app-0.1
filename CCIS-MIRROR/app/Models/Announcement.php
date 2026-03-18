<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'table_announcement';
    protected $primaryKey = 'announcement_id';

    // 1. ADDED: This tells Laravel to ALWAYS fetch the author and their profile 
    // whenever an Announcement is queried. You'll never miss a profile picture again!
    protected $with = ['author.profile']; 

    protected $fillable = [
        'board_id',
        'author_id',
        'title',
        'content',
        'topic',
        'likes_count',
    ];

    /**
     * Get the attachments for the announcement.
     */
    public function attachments(): HasMany 
    {
        return $this->hasMany(AnnouncementAttachment::class, 'announcement_id', 'announcement_id');
    }

    /**
     * Get the author of the announcement.
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }

    /**
     * Get the board this announcement belongs to.
     */
    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id', 'board_id');
    }
}