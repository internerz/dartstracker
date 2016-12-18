<?php

namespace App\Http\Controllers;

use App\Game;
use App\Leg;
use App\Mode;
use App\GameOrder;
use App\Point;
use App\User;
use Illuminate\Http\Request;

class GameController extends Controller
{

    public function index()
    {
        $games = \Auth::user()->games()->with('mode')->get()->sortByDesc('created_at');

        return view('game.index', compact('games'));
    }


    public function view(Game $game)
    {
        if (in_array($game->id, \Auth::user()->games()->get()->pluck('id')->toArray())) {
            $game = Game::with('users')->with('legs')->with('mode')->find($game->id);

            $currentLeg = $game->getCurrentLeg();

            return view('game.view', compact('game', 'currentLeg'));
        } else {
            abort(404, 'You are not part of this game.');
        }
    }


    public function create()
    {
        $modes = Mode::all();

        return view('game.create', compact('modes'));
    }


    public function store(Request $request)
    {
        // TODO: validate user input
        // TODO: throw errors
        $game = new Game();
        $game->mode_id = $request->get('mode');
        $game->ruleset = $request->get('ruleset');
        $game->number_of_legs_to_win = $request->get('legs');
        $game->save();

        // TODO: validate, check if user is existing
        $opponents = json_decode($request->get('opponents'));
        $opponents[] = \Auth::user()->id;
        $game->users()->sync($opponents);
        $this->createLeg($game);
        $this->setOrder($game);

        return redirect()->route('view-game', $game->id);
    }


    public function storePoints(Request $request)
    {
        // TODO: verfiy user data
        $leg = Leg::find($request->get('leg'));
        $pointsArray = json_decode($request->get('points'));

        foreach ($pointsArray as $data) {
            $point = new Point;
            $point->points = $data[0];
            $point->multiplier = $data[1];
            $point->user_id = $request->get('user');
            $leg->points()->save($point);

        }

        $response = [
            'nextPlayerId'   => $leg->game->getCurrentPlayer()->id,
            'nextPlayerName' => $leg->game->getCurrentPlayer()->name,
            'playerPoints' => $leg->game->getCurrentPointsOfAllPlayer()
        ];

        return \Response::json(json_encode($response));
    }


    public function createLeg(Game $game)
    {
        $leg = new Leg;
        $game->legs()->save($leg);
    }


    private function setOrder(Game $game)
    {
        $users = $game->users->shuffle();

        foreach ($users as $position => $user) {
            $order = new GameOrder;
            $order->game_id = $game->id;
            $order->user_id = $user->id;
            $order->position = $position;
            $order->state_id = 1;
            $order->save();
        }
    }

    public function storeState(Request $request)
    {
        // TODO: verfiy user data
        $game = Game::find($request->get('game'));
        $state_id = $request->get('state_id');
        $user = User::find($request->get('user'));

        //dd($game);

        $gameOrder = $game->orders()->where('user_id', $user->id)->get()->first();
        $gameOrder->state_id = $state_id;
        $gameOrder->save();

        //dd($gameOrder);

        $response = [
            'currentState'   => $game->getCurrentState($user)
        ];

        return \Response::json(json_encode($response));
    }
}
