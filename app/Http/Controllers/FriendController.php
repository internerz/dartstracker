<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use League\Flysystem\Exception;

class FriendController extends Controller
{

    public function index()
    {
        $friends = Auth::user()->friends()->get();

        return view('friends', compact('friends'));
    }

/*
    public function add(User $user)
    {
        if (Auth::check()) {
            $friend = new Friend();
            $friend->user_id = Auth::id();
            $friend->friends_id = $user->id;
            $friend->save();

            $friend = new Friend();
            $friend->user_id = $user->id;
            $friend->friends_id = Auth::id();
            $friend->save();
        }

        return back();
    }


    public function remove(User $user)
    {
        if (Auth::check()) {
            $friends = Friend::where('user_id', $user->id)->where('friends_id', Auth::id())->orWhere('user_id',
                Auth::id())->where('friends_id', $user->id)->get();

            foreach ($friends as $friend) {
                $friend->delete();
            }
        }

        return back();
    }
*/

    public function add(Request $request)
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
