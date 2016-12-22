<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFriendRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class FriendController extends Controller
{

    public function index()
    {
        $friends = Auth::user()->friends()->get();

        return view('friends.index', compact('friends'));
    }

    public function add(AddFriendRequest $request)
    {
        $user = Auth::user();
        $user->friends()->attach($request->friend_id);   // add friend
        $friend = User::find($request->friend_id);       // find your friend, and...
        $friend->friends()->attach($user->id);  // add yourself, too

        return back();
    }


    public function remove(Request $request)
    {
        $user = Auth::user();
        $user->friends()->detach($request->friend_id);   // remove friend
        $friend = User::find($request->friend_id);       // find your friend, and...
        $friend->friends()->detach($user->id);  // remove yourself, too

        return back();
    }
}
