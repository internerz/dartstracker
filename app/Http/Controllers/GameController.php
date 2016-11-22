<?php

namespace App\Http\Controllers;

use App\Game;
use App\Leg;
use App\Mode;
use App\Point;
use App\User;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index() {
        $games = Game::with('mode')->get()->sortByDesc('created_at');

        return view('game.index', compact('games'));
    }

    public function view(Game $game) {
        $game = Game::with('users')->with('legs')->with('mode')->where('id', $game->id)->get()->first();
        // TODO: error handling when no game was found (e.g. a user entered a non-existing id)

        $currentLeg = Game::find($game->id)->legs()->where('winner_user_id', null)->first();

        return view('game.view', compact('game', 'currentLeg'));
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
        $game->number_of_legs_to_win = $request->get('legs');
        $game->save();

        // TODO: replace with for loop
        // TODO: validate, check if user is existing
        $game->users()->sync(array(
            \Auth::user()->id,
            $request->get('opponent'),
        ));

        $this->createLeg($game);

        return redirect()->to('/game/'.$game->id);
    }

    public function storePoints(Request $request) {
        // TODO: verfiy user data
        $leg = Leg::find($request->get('leg'));
        $pointsArray = json_decode($request->get('points'))->points;

        foreach ($pointsArray as $data) {
            $point = new Point;
            $point->points = $data[0];
            $point->multiplier = $data[1];
            $point->user_id = $request->get('user');
            $leg->points()->save($point);
        }

        $response = [
            'nextPlayerId' => $this->getNextPlayer()->id,
            'nextPlayerName' => $this->getNextPlayer()->name,
        ];
        return \Response::json(json_encode($response));
    }

    public function createLeg(Game $game) {
        $leg = new Leg;
        $game->legs()->save($leg);
    }

    private function getNextPlayer() {
        return User::find(2);
    }
}
