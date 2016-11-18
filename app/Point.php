<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    public function leg() {
        return $this->belongsTo(Leg::class);
    }
}
