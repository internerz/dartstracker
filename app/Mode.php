<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mode extends Model
{
    public function game() {
        return $this->belongsTo(Game::class);
    }
}
