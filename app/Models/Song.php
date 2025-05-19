<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = ['title', 'artist', 'file','duration','order', 'scheduled_time', 'playlist_id'];

    public function playlist()
    {
        return $this->belongsTo(Playlist::class);
    }
}
