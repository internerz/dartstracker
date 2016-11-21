<?php

namespace App\Http\Controllers;

use App\Game;
use App\Mode;
use App\User;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index() {
        $games = Game::all();

        return view('game.index', compact('games'));
    }

    public function view(Game $game) {
        $game = Game::find($game->id);
        // TODO: error handling when no game was found (e.g. a user entered a non-existing id)

        return view('game.view', compact('game'));
    }

    public function create() {
        $modes = Mode::all();

        return view('game.create', compact('modes'));
    }

    public function store(Request $request) {
        // TODO: validate user input
        // TODO: throw errors
        $game = new Game();
        $game->mode_id = $request->get('mode');
        $game->ruleset = $request->get('ruleset');
        $game->save();

        // TODO: replace with for loop
        // TODO: validate, check if user is existing
        $game->users()->sync(array(
            \Auth::user()->id,
            $request->get('opponent'),
        ));

        return redirect()->to('/game/'.$game->id);
    }

    public function storePoints(Request $request, Game $game) {
        // TODO: verfiy user data

        dd($request->all());
    }
}
