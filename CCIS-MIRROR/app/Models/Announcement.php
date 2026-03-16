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
    public function attachments()
    {
        return $this->hasMany(AnnouncementAttachment::class, 'announcement_id', 'announcement_id');
    }
    public function author(): BelongsTo
    {
        
        return $this->belongsTo(User::class, 'author_id', 'user_id');
    }


    public function board(): BelongsTo
    {
     
        return $this->belongsTo(Board::class, 'board_id', 'board_id');
    }
}