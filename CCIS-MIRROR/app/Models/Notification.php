<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    // Matches the table name in your migration
    protected $table = 'table_notification';

    // Matches the primary key in your migration
    protected $primaryKey = 'notification_id';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'reciepient_id',
        'announcement_id',
        'event_id',
        'message',
        'is_read',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_read' => 'boolean',
    ];

    // --- Relationships ---

    /**
     * Get the user who receives the notification.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'reciepient_id', 'user_id');
    }

    /**
     * Get the announcement associated with the notification.
     */
    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'announcement_id', 'announcement_id');
    }

    /**
     * Get the event associated with the notification.
     */
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }
}