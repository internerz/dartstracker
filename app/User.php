<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function games()
    {
        return $this->belongsToMany(Game::class);
    }


    public function points()
    {
        return $this->hasMany(Point::class);
    }


    public function order()
    {
        return $this->hasMany(GameOrder::class);
    }


    public function friends()
    {
        return $this->belongsToMany(User::class, 'friend_user', 'user_id', 'friend_id');
    }

    public function rounds() {
        return $this->hasMany(Round::class);
    }

}
