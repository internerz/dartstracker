<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class FriendController extends Controller
{

    public function index()
    {
        $friends = Auth::user()->friends()->get();

        return view('friends', compact('friends'));
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'friend_id' => 'required|unique:friend_user|min:1'
        ]);

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
