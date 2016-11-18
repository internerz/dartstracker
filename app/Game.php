<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public function users() {
        return $this->hasManyThrough(User::class, Game::class);
    }

    public function legs() {
        return $this->hasMany(Leg::class);
    }

    public function mode() {
        return $this->hasOne(Mode::class);
    }
}
