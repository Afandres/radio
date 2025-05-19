<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Programming extends Model
{
    protected $fillable = ['song_id','position'];

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}
