<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{

    protected $fillable = [
        'mode',
        'ruleset',
        'number_of_legs_to_win',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class);
    }


    public function legs()
    {
        return $this->hasMany(Leg::class);
    }


    public function mode()
    {
        return $this->belongsTo(Mode::class);
    }


    public function orders()
    {
        return $this->hasMany(GameOrder::class);
    }


    /**
     * @return \App\Leg
     */
    public function getCurrentLeg()
    {
        return $this->legs()->where('winner_user_id', null)->first();
    }


    /**
     * @param \App\User $user
     */
    public function setLegWinner(User $user)
    {
        $leg = $this->getCurrentLeg();
        $leg->winner_user_id = $user->id;
        $leg->save();
    }


    /**
     * @return boolean
     */
    public function hasNextLeg()
    {
        return $this->legs()->count() < $this->number_of_legs_to_win;
    }


    /**
     * @return boolean
     */
    public function hasPoints()
    {
        return $this->getCurrentLeg()->points->count() > 0;
    }


    /**
     * @return \App\User
     */
    public function getLastPlayer()
    {
        if ($this->hasPoints()) {
            return User::find($this->legs->last()->points->last()->user_id);
        } else {
            return $this->orders->first()->user;
        }
    }


    /**
     * @return \App\User
     */
    public function getCurrentPlayer()
    {
        $playersInGame = GameOrder::where('game_id', $this->id)->count();
        $currentPlayer = $this->getLastPlayer();

        if ($currentPlayer->order->where('game_id', $this->id)->first()->position + 1 == $playersInGame) {
            $position = 0;
        } else {
            $position = $currentPlayer->order->where('game_id', $this->id)->first()->position + 1;
        }

        return User::find(GameOrder::where('game_id', $this->id)->where('position', $position)->get()->first()->user_id);
    }

    public function getCurrentPointsOfPlayer(User $user) {
        $points = Point::where('user_id', $user->id)->where('leg_id', $this->getCurrentLeg()->id)->get()->map(function($item){
            return $item->points * $item->multiplier;
        })->sum(function($value){
            return $value;
        });

        $modeScore = $this->mode->first()->score;

        return $modeScore - $points;
    }

    public function getCurrentPointsOfAllPlayer(){
        $users = $this->users()->get();

        $points = array();

        foreach ($users as $user){
            $points[$user->id] = $this->getCurrentPointsOfPlayer($user);
        }

        return $points;
    }

    public function getCurrentState(User $user) {
        $stateId = $this->orders()->where('user_id', $user->id)->get()->first()->state_id;

        $state = State::where('id', $stateId)->get()->first();
        return $state;
    }

    public function setCurrentState(User $user, int $id) {
        // TODO: set State to next State given by the game

        $gameOrder = $this->orders()->where('user_id', $user->id)->get()->first();
        $gameOrder->state_id = $id;
        $gameOrder->save();
    }

    public function getCurrentStateOfAllPlayer(){
        $users = $this->users()->get();

        $states = array();

        foreach($users as $user) {
            $states[$user->id] = $this->getCurrentState($user);
        }

        return $states;
    }
}
