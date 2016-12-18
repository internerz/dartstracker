@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Game #{{ $game->id }}</h1>

                Mode: {{ $game->mode->name }}<br/>
                Ruleset: {{ $game->ruleset }}<br/>
                Number of legs to win: {{ $game->number_of_legs_to_win }}<br/>
                Current leg: {{ $game->legs->count() }}<br/>
                Last player: {{ $game->getLastPlayer()->name }}<br/>
                Current player: {{ $game->getCurrentPlayer()->name }}<br/>
                @if ($currentLeg)
                    Current leg ID: {{ $currentLeg->id }}<br/>
                @endif
                Players: @foreach ($game->users as $user)
                    {{ $user->name }},
                @endforeach

                <h2>Score</h2>

                <div class="row" id="scoreBoard">
                    @foreach ($game->users as $user)
                        <div class="col-md-{{ 12/count($game->users) }} col-xs-6">
                            <a href="{{ route('show-user', $user->id) }}">{{ $user->name }}</a><br/>
                            <span class="score"
                                  id="id-{{$user->id}}">501 : {{ $game->getCurrentPointsOfPlayer($user)}}</span><br />
                            <span>Current State: {{$game->getCurrentState($user)->name}}</span>
                        </div>
                    @endforeach
                </div>

                @if ($currentLeg)
                    <div class="row" id="points">
                        <div class="col-lg-10 col-lg-offset-1">
                            @include('game.dartboard')
                        </div>

                        <div class="col-sm-12">
                            <div id="currentScoreElement" class="score">
                                <h2 id="score" class="score">
                                    <span id="playerName">{{ $game->getCurrentPlayer()->name }}</span> darts:
                                    <span id="playerScore">0</span>
                                </h2>
                            </div>

                            <button type="submit" class="btn btn-primary" id="submit" disabled>Submit</button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @if ($currentLeg)
        <script type="text/javascript">
            $(document).ready(function () {
                        var points = [];
                        var button = $('#submit');
                        var csrf_token = $('meta[name="csrf-token"]').attr('content');
                        var board = $('#board');
                        var currentScoreElement = $('#currentScoreElement');
                        var playerNameElement = $('#playerName');
                        var playerScoreElement = $('#playerScore');
                        var scoreBoard = $('#scoreBoard');

                        var startingScore = 0;
                        var currentScore = startingScore;

                        var bullseye = 50;
                        var outer = 25;
                        var singleMultiplier = 1;
                        var doubleMultiplier = 2;
                        var trippleMultiplier = 3;

                        var Game = function (players) {
                            var self = this;
                            this.players = [];
                            this.states = [new DoubleIn(this), new DoubleOut(this)];
                            players.forEach(function (element, index, array) {
                                self.players.push(new Player(element, self.states));
                            });

                            this.currentPlayer = this.players[1];

                            this.setCurrentPlayer = function (playerObject) {
//                                console.log("ob", playerObject)
                                var currentPlayer = self.players.find(function (player) {
                                    return player.id == playerObject.id;
                                });

                                self.currentPlayer = currentPlayer;
                            };

                            this.gameOver = function () {
                                // set gameOver
                            }

                            // handle Input
                            this.handleInput = function (el) {
                                this.currentPlayer.currentState.handleInput(el);
                            }
                        }

                        var Player = function (player, states) {
                            var self = this;
                            this.id = player.id;
                            this.name = player.name;
                            this.points = 0;
                            this.states = states;       // überflüssig? einfach game.states?
                            this.statesIndex = 0;
                            this.currentState = this.states[self.statesIndex];

                            this.nextState = function () {
                                // TODO: not increment statesIndex but set states to a specific state --> no state ordering

                                self.statesIndex++;
                                self.currentState = self.states[self.statesIndex];

                                // TODO: AJAX-Call
                                
                            }
                        }

                        var DoubleIn = function (game) {
                            this.game = game;

                            this.handleInput = function (el) {

                                var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);

                                if (points.length < 3) {
                                    switch (scoreParameters[0]) {
                                        case "s":
                                            points.push([0, singleMultiplier]);
                                            break;
                                        case "d":
                                            points.push([scoreParameters[1], doubleMultiplier]);
                                            game.currentPlayer.nextState();
                                            break;
                                        case "t":
                                            points.push([0, trippleMultiplier]);
                                            break;
                                        case "Bull":
                                            points.push([0, singleMultiplier]);
                                            break;
                                        case "Outer":
                                            points.push([0, singleMultiplier]);
                                            break;
                                        default:
                                            console.log("something bad happened");
                                    }

                                    updateGui(el);
                                }

                                if (points.length == 3) {
                                    button.prop('disabled', false);
                                }
                            }
                        }

                        var DoubleOut = function (game) {
                            this.game = game;

                            this.handleInput = function (el) {
                                var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);

                                if (points.length < 3) {
                                    switch (scoreParameters[0]) {
                                        case "s":
                                            points.push([scoreParameters[1], singleMultiplier]);
                                            break;
                                        case "d":
                                            points.push([scoreParameters[1], doubleMultiplier]);
                                            break;
                                        case "t":
                                            points.push([scoreParameters[1], trippleMultiplier]);
                                            break;
                                        case "Bull":
                                            points.push([bullseye, singleMultiplier]);
                                            break;
                                        case "Outer":
                                            points.push([outer, singleMultiplier]);
                                            break;
                                        default:
                                            console.log("something bad happened");
                                    }

                                    updateGui(el);
                                }

                                if (points.length == 3) {
                                    button.prop('disabled', false);
                                }
                            }
                        }

                        var Playing = function (game) {
                            this.game = game;

                            this.handleInput = function () {
                                // add the points according to the state

                                // certain condition -> player.nextState()
                            }
                        }

                        var gameInfo = {!! json_encode($game) !!};

                        var players = [];

                        gameInfo.users.forEach(function (element, index, array) {
                            players.push(element);
                        });

                        var game = new Game(players);

                        var currentPlayer = {!! json_encode($game->getCurrentPlayer()) !!};
                        game.setCurrentPlayer(currentPlayer);

                        var player = {
                            id: "{{ \Auth::user()->id }}",
                            name: "{{ \Auth::user()->name }}",
                            points: ""
                        };

                        board.find("#areas g").children().hover(
                                function () {
                                    $(this).css("opacity", "0.5").css('cursor', 'pointer');
                                },
                                function () {
                                    $(this).css("opacity", "1");
                                }
                        ).click(function () {
                            game.handleInput($(this));
                        });

                        button.click(function () {
                            var data = JSON.stringify(points);
                            button.prop('disabled', true);
                            points = [];

                            $.ajax({
                                type: "POST",
                                url: '{{ url()->current() }}',
                                data: {
                                    _token: csrf_token,
                                    user: game.currentPlayer.id,
                                    leg: "{{ $currentLeg->id }}",
                                    points: data,
                                },
                                success: function (response) {
//                                    player = {
//                                        id: JSON.parse(response)['nextPlayerId'],
//                                        name: JSON.parse(response)['nextPlayerName'],
//                                        points: JSON.parse(response)['playerPoints']
//                                    };

                                    // TODO: auslagern?
                                    var playerPoints = JSON.parse(response)['playerPoints'];

                                    for (var playerId in playerPoints) {
                                        var player = game.players.find(function (player) {
                                            return player.id == playerId;
                                        });
                                        player.points = playerPoints[playerId];
                                    }

                                    game.players.forEach(function (element, index, array) {
                                        if (element.id == JSON.parse(response)['nextPlayerId']) {
                                            game.setCurrentPlayer(element);
                                        }
                                    });

                                    removePointElements();
                                    updateScoreElement(startingScore);
                                    updatePlayerStrings();
                                    updatePlayerPoints();
                                    currentScore = 0;
                                },
                                dataType: 'json'
                            });

                            return false;
                        });

                        function updateGui(el) {
                            addPointsElement(getScorePoints(el));
                            currentScore = currentScore + getScorePoints(el);
                            updateScoreElement(currentScore);
                        }

                        function updatePlayerStrings() {
                            playerNameElement.text(game.currentPlayer.name);
                        }

                        function updatePlayerPoints() {
                            game.players.forEach(function (element, index, array) {
                                var field = scoreBoard.find('#id-' + element.id);
                                field.text(element.points);
                            });
                        }

                        function addPointsElement(score) {
                            // TODO: dont append the score out the element clicked but the value evaluated by the state. e.g. DoubleIn->1 => show 0
                            currentScoreElement.append('<div class="col-md-4">'
                                    + score + '<span class="glyphicon glyphicon-remove"></span></div>');
                            var lastScoreElement = currentScoreElement.find('div').last();

                            lastScoreElement.find('span').click(function () {
                                currentScore = currentScore - score;
                                points.splice(currentScoreElement.find('div').index($(this).parent()), 1);
                                updateScoreElement(currentScore);
                                lastScoreElement.remove();

                                if (points.length < 3) {
                                    button.prop('disabled', true);
                                }
                            });
                        }

                        function updateScoreElement(score) {
                            playerScoreElement.text(score);
                        }

                        function removePointElements() {
                            currentScoreElement.find('div').remove();
                        }

                        function getScorePoints(el) {
                            var scoredPoints = 0;
                            var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);

                            switch (scoreParameters[0]) {
                                case "s":
                                    scoredPoints = 1 * scoreParameters[1];
                                    break;
                                case "d":
                                    scoredPoints = 2 * scoreParameters[1];
                                    break;
                                case "t":
                                    scoredPoints = 3 * scoreParameters[1];
                                    break;
                                case "Bull":
                                    scoredPoints = 50;
                                    break;
                                case "Outer":
                                    scoredPoints = 25;
                                    break;
                                default:
                                    console.log("something happened");
                            }

                            return scoredPoints;
                        }
                    }
            );
        </script>
    @endif
@endsection