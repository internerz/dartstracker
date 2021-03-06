<?php

namespace App\Http\Controllers;

use App\Game;
use App\Leg;
use App\Mode;
use App\GameOrder;
use App\Point;
use App\Round;
use App\State;
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
        $class = 'game-view';

        if (in_array($game->id, \Auth::user()->games()->get()->pluck('id')->toArray())) {
            $game = Game::with('users')->with('legs')->with('mode')->find($game->id);

            if ($game->winner_user_id == null) {
                $currentLeg = $game->getCurrentLeg();
                $string = file_get_contents("finishes.json");
                $finishes = json_decode($string, true);

                return view('game.view', compact('game', 'currentLeg', 'finishes', 'class'));
            } else {
                $gameInformation = $game->getInformation();

                return view('game.aftermath', compact('game', 'gameInformation'));
            }
        } else {
            abort(404, 'You are not part of this game.');
        }
    }


    public function viewGuest(Request $request)
    {
        $class = 'game-view game-view-guest loading';
        $mode = Mode::find($request->get('mode'));
        $string = file_get_contents("finishes.json");
        $finishes = json_decode($string, true);
        $states = json_encode(State::find([
            intval($request->get('starting-rule')),
            3,
            intval($request->get('ending-rule')),
        ])->toArray());

        return view('game.view-guest', compact(
            'mode',
            'finishes',
            'states',
            'class'
        ));
    }


    public function create(Request $request)
    {
        $friend = User::find($request->friend);
        $modes = Mode::all();
        $states = State::all()->toJson();

        return view('game.create', compact('modes', 'friend', 'states'));
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
        $game->states()->sync([$request->get('starting-rule'), 3, $request->get('ending-rule')]);
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
        $game = Game::find($request->get('game'));

        foreach ($pointsArray as $data) {
            $point = new Point;
            $point->points = $data[0];
            $point->multiplier = $data[1];
            $point->user_id = $request->get('user');
            $leg->points()->save($point);

        }

        $this->storeRound($request);    // behandelt die gleichen Daten
        $round = Round::where('user_id', $request->get('user'))->where('leg_id', $request->get('leg'))->orderBy('id',
            'desc')->first();
        $response = [];
        $legWon = false;
        if ($round->rest == 0) {
            $legWon = true;

            $user = User::find($request->get('user'));
            $game->setLegWinner($user);

            $users = $game->users;
            $newLeg = true;
            foreach ($users as $user) {
                if ($game->getCurrentLegWins($user) == $game->number_of_legs_to_win) {
                    $game->setGameWinner($user);
                    $newLeg = false;
                    $response = [ 'gameWon' => true ];
                    //return redirect()->route('view-game', $game->id); doesnt work with ajax-calls?
                    break;
                }
            }

            if ($newLeg) {
                $this->createLeg($game);
                $this->resetStates($game);
                $response = [
                    'nextPlayerId'   => $leg->game->getCurrentPlayer()->id,
                    'nextPlayerName' => $leg->game->getCurrentPlayer()->name,
                    'playerPoints'   => $leg->game->getCurrentPointsOfAllPlayer(),
                    'legWon'         => $legWon
                ];
            }
        } else {
            $response = [
                'nextPlayerId'   => $leg->game->getCurrentPlayer()->id,
                'nextPlayerName' => $leg->game->getCurrentPlayer()->name,
                'playerPoints'   => $leg->game->getCurrentPointsOfAllPlayer(),
                'legWon'         => $legWon
            ];
        }

        return \Response::json(json_encode($response));
    }


    public function createLeg(Game $game)
    {
        $leg = new Leg;
        $game->legs()->save($leg);
        $leg->users()->sync($game->users()->get()->pluck('id')->toArray());
    }


    private function setOrder(Game $game)
    {
        $users = $game->users->shuffle();

        foreach ($users as $position => $user) {
            $order = new GameOrder;
            $order->game_id = $game->id;
            $order->user_id = $user->id;
            $order->position = $position;
            $startState = $game->states()->where('phase', 'Start')->first();
            $order->state_id = $startState->id;
            $order->save();
        }
    }


    public function resetStates(Game $game)
    {
        $users = $game->users;

        foreach ($users as $user) {
            $order = GameOrder::where('game_id', $game->id)->where('user_id', $user->id)->first();
            $startState = $game->states()->where('phase', 'Start')->first();
            $order->state_id = $startState->id;
            $order->save();
        }
    }


    public function storeState(Request $request)
    {
        // TODO: verfiy user data
        $game = Game::find($request->get('game'));
        $state_id = $request->get('state_id');
        $user = User::find($request->get('user'));

        $gameOrder = $game->orders()->where('user_id', $user->id)->get()->first();
        $gameOrder->state_id = $state_id;
        $gameOrder->save();

        $response = [
            'currentState' => $game->getCurrentState($user)
        ];

        return \Response::json(json_encode($response));
    }


    public function storeRound(Request $request)
    {
        $leg = Leg::find($request->get('leg'));
        $game = Game::find($request->get('game'));
        $user = User::find($request->get('user'));
        $pointsArray = json_decode($request->get('points'));

        //var_dump("test", $pointsArray);
        $sum = 0;
        foreach ($pointsArray as $data) {
            $sum += $data[0] * $data[1];
        }

        $round = new Round;
        $round->user_id = $request->get('user');
        $round->score = $sum;
        $round->rest = $game->getCurrentPointsOfPlayer($user);
        $leg->rounds()->save($round);

        return false;
    }
}
