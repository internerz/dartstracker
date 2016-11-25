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

    public function orders() {
        return $this->hasMany(GameOrder::class);
    }

    /**
     * @return \App\Leg
     */
    public function getCurrentLeg() {
        return $this->legs()->where('winner_user_id', null)->first();
    }

    /**
     * @param \App\User $user
     */
    public function setLegWinner(User $user) {
        $leg = $this->getCurrentLeg();
        $leg->winner_user_id = $user->id;
        $leg->save();
    }

    /**
     * @return boolean
     */
    public function hasNextLeg() {
        return $this->legs()->count() < $this->number_of_legs_to_win;
    }

    /**
     * @return boolean
     */
    public function hasPoints() {
        return $this->getCurrentLeg()->points->count() > 0;
    }

    /**
     * @return \App\User
     */
    public function getCurrentPlayer() {
        if ($this->hasPoints()) {
            return User::find($this->legs->last()->points->last()->user_id);
        } else {
            return $this->orders->first()->user;
        }
    }

    /**
     * @return \App\User
     */
    public function getNextPlayer() {
        $playersInGame = GameOrder::where('game_id', $this->id)->count();
        $currentPlayer = $this->getCurrentPlayer();

        if ($currentPlayer->order->position + 1 == $playersInGame) {
            $position = 0;
        } else {
            $position = $currentPlayer->order->position + 1;
        }
        var_dump(GameOrder::where('game_id', $this->id)->where('position', $position)->get()->first()->user_id);

        return User::find(GameOrder::where('game_id', $this->id)->where('position', $position)->get()->first()->user_id);
    }
}
