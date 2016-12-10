<?php

namespace App\Http\Controllers;

use App\Friend;
use App\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function find(Request $request)
    {
        if (\Auth::check()) {
            $users = User::where('name', 'LIKE', '%'.$request->get('term').'%')->take(10)->get();

            foreach ($users as $key => $user) {
                if ($user->id != \Auth::user()->id) {
                    $userArray[$key]['id'] = $user->id;
                    $userArray[$key]['value'] = $user->name;
                }
            }

            return json_encode($userArray);
        }
    }


    public function show(User $user)
    {
        $user = User::with('friends')->find($user->id);
        $areFriends = !is_null(Friend::where('user_id', \Auth::user()->id)->where('friends_id', $user->id)->first());

        return view('user.show', compact('user', 'areFriends'));
    }


    public function profile()
    {
        if (\Auth::check()) {
            $user = \Auth::user();
            $friends = User::whereIn('id', $user->friends->pluck('friends_id')->toArray())->get();

            return view('user.show', compact('user', 'friends'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function edit() {
        if (\Auth::check()) {
            $user = \Auth::user();

            return view('user.edit', compact('user'));
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function store(Request $request) {
        $user = \Auth::user();
        $rules = [];

        if ($user->name != $request->get('name')) {
            $rules['name'] = 'required|unique:users|max:255';
            $user->name = $request->get('name');
        }

        if ($user->email != $request->get('email')) {
            $rules['email'] = 'required|email|unique:users|max:255';
            $user->email = $request->get('email');
        }

        if (strlen($request->get('password')) > 0) {
            $rules['password'] = 'required|min:6|confirmed';
            $user->password = bcrypt($request->get('password'));
        }

        if (count($rules) > 0) {
            $this->validate($request, $rules);

            $user->save();

            return back()->with('confirmation', true);
        } else {
            return back();
        }


    }
}
