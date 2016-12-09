<?php

namespace App\Http\Controllers;

use App\Friend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class FriendController extends Controller
{

    public function index() {
        $friends = Friend::where('user_id', Auth::id())->get();
        $friends_names = [];
        foreach ($friends as $friend) {
            array_push($friends_names, User::find($friend->friends_id)->name);
        }
        return view('friends', compact('friends', 'friends_names'));
    }

    public function store(Request $request) {
        if (Auth::check()) {
            $friend = new Friend();
            $friend->user_id = Auth::id();
            $friend->friends_id = $request->get('friend_id');
            $friend->save();
        }
        return back();
    }

    public function deleteFriend(Request $request) {
        Friend::where('friends_id', $request->friends_id)->delete();

        return back();
    }
}
