<?php

namespace App\Http\Controllers;

use App\Mode;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function modes() {
        $modes = Mode::all();

        return view('modes', compact('modes'));
    }

    public function storeMode(Request $request) {

        $mode = new Mode();
        $mode->name = $request->get('name');
        $mode->save();

        return back();
    }

    public function deleteMode(Request $request) {
        Mode::find($request->id)->delete();

        return back();
    }
}
