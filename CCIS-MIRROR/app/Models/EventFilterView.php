<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFilterView extends Model
{
    protected $table = 'view_event_filters';

    protected $primaryKey = 'event_id';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function board()
    {
        return $this->belongsTo(Board::class, 'board_id', 'board_id');
    }
}