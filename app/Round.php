<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    public $timestamps = false;

    public function leg() {
        return $this->belongsTo(Leg::class);
    }

    public function user() {
        return $this->hasOne(User::class);
    }
}
