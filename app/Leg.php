<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leg extends Model
{
    public $timestamps = false;

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function points() {
        return $this->hasMany(Point::class);
    }

    public function users() {
        return $this->hasMany(User::class);
    }
}
