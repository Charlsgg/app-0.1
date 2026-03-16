<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Event extends Model
{
    use SoftDeletes;
    
    protected $table = 'table_events';
    protected $primaryKey = 'event_id';
    public $incrementing = true; 

    protected $fillable = [
    'event_id',
    'user_id', // Make sure this is here!
    'title',
    'description',
    'event_date',
];
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class, 'board_id', 'board_id');
    }
}