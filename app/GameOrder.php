<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GameOrder extends Model
{
    public $timestamps = false;

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function game() {
        return $this->belongsTo(Game::class);
    }
}
