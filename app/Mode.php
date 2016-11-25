<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mode extends Model
{
    public $timestamps = false;

    public function game() {
        return $this->belongsToMany(Game::class);
    }
}
