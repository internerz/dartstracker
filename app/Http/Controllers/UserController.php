<?php

namespace App\Http\Controllers;

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
}
