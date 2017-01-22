<?php

namespace App\Http\Controllers;

use App\Friend;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function find(Request $request)
    {
        if (\Auth::check()) {
            $nameIsLike = '%'.$request->get('term').'%';

            if ((boolean) $request->get('friendsFirst')) {
                $users = \Auth::user()->friends()->where('name', 'LIKE', $nameIsLike)->take(10)->get();
                if ($users->count() < 10) {
                    $users = $users->merge(
                        User::where('name', 'LIKE', $nameIsLike)->take(10 - $users->count())->get()
                    );
                }
            } else {
                $users = User::where('name', 'LIKE', $nameIsLike)->take(10)->get();
            }

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
        $areFriends = in_array(\Auth::id(), $user->friends()->pluck('friend_id')->toArray());
        $userLegPointStats = $user->getLegPointStatistics();
        dd($userLegPointStats);

        return view('user.show', compact('user', 'areFriends', 'userLegPointStats'));
    }


    public function profile()
    {
        if (\Auth::check()) {
            $user = \Auth::user();
            $friends = $user->friends()->get();
            $userLegPointStats = $user->getLegPointStatistics();

            return view('user.show', compact('user', 'friends', 'userLegPointStats'));
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

    public function markNotificationAsRead(Request $request) {
        $notification = \Auth::user()->notifications()->where('id', $request->get('notification'))->first();
        $notification->read_at = Carbon::now();
        $notification->save();

        return back();
    }
}
