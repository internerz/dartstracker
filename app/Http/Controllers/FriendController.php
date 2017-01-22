<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddFriendRequest;
use App\FriendStatus;
use App\Notifications\FriendAdded;
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

        $friendStatus = new FriendStatus();
        $friendStatus->user_id = $user->id;
        $friendStatus->friend_id = $request->friend_id;
        $friendStatus->status = $friendStatus->getFriendStatus('pending');
        $friendStatus->save();

        $friend->notify(new FriendAdded($user));

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

    public function accept(Request $request, User $user) {
        // set friend status
        $this->changeFriendStatus('accepted', $user);

        // mark notification as read
        app('App\Http\Controllers\UserController')->markNotificationAsRead($request);

        return back();
    }

    public function reject(Request $request, User $user) {
        // set friend status
        $this->changeFriendStatus('rejected', $user);

        // get notification
        $notification = \Auth::user()->notifications()->where('id', $request->get('notification'))->first();

        // delete notification
        $notification->delete();

        // add friend id to request
        $request['friend_id'] = $notification->data['user_id'];

        // delete friendship
        $this->remove($request);

        return back();
    }

    private function changeFriendStatus(string $statusName, User $user) {
        $status = FriendStatus::where('user_id', $user->id)->where('friend_id', \Auth::id())->first();
        $status->status = FriendStatus::getFriendStatus($statusName);
        $status->save();
    }
}
