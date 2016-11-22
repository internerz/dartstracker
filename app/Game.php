<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'mode', 'ruleset', 'number_of_legs_to_win',
    ];

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function legs() {
        return $this->hasMany(Leg::class);
    }

    public function mode() {
        return $this->belongsTo(Mode::class);
    }
}