<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Championship extends Model
{
    public function games() {
        return $this->hasManyThrough(Game::class, Championship::class);
    }
}
