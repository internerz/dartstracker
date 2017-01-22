<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FriendStatus extends Model
{

    public static $FRIEND_STATUSES = [
        0 => 'pending',
        1 => 'accepted',
        2 => 'rejected',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'friend_id',
        'status',
    ];


    /**
     * Gets the status id by status name.
     *
     * @param string $status
     *
     * @return mixed
     */
    public static function getFriendStatus(string $status)
    {
        return array_search($status, FriendStatus::$FRIEND_STATUSES);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
