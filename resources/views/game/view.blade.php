@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline">
                    @foreach ($game->users as $i => $player)
                        {{ $player->name }}
                        @if ($i < count($game->users) - 1)
                            vs.
                        @endif
                    @endforeach
                </h2>
            </div>

            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-7 col-md-offset-0 col-lg-8">
                <div class="points" id="points">
                    @include('game.dartboard')
                </div>
            </div>

            <div class="col-sm-12 col-md-5 col-lg-4">
                <div class="currentSore">
                    <h3 id="score" class="score">
                        <span id="playerName">{{ $game->getCurrentPlayer()->name }}</span> darts:
                        <span id="playerScore" class="playerScore">0</span>
                    </h3>
                    <div id="currentScoreElement" class="row currentScoreElement"></div>
                    <button type="submit" class="btn btn-primary" id="submit" disabled>Save Score</button>
                </div>

                <h3>Score</h3>

                <div class="row" id="scoreBoard">
                    @foreach ($game->users as $user)
                        <div class="col-md-{{ 12/count($game->users) }} col-xs-6">
                            <a href="{{ route('show-user', $user->id) }}">{{ $user->name }}</a><br/>
                            <span class="score"
                                  id="id-{{$user->id}}">{{ $game->getCurrentPointsOfPlayer($user)}}</span><br/>
                            <span id="state-{{$user->id}}">Current State: {{$game->getCurrentState($user)->name}}</span><br/>
                            <span id="finish-{{$user->id}}">Finish: -</span>
                        </div>
                    @endforeach
                </div>

                <div class="panel-group gameinfo" id="accordion" role="tablist" aria-multiselectable="true">
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="gameinfoH">
                            <a role="button" class="toggle" data-toggle="collapse" data-parent="#accordion"
                               href="#gameinfo" aria-expanded="false" aria-controls="gameinfo">
                                Game info
                                <span class="glyphicon glyphicon-plus collapsed"></span>
                                <span class="glyphicon glyphicon-minus extended"></span>
                            </a>
                        </div>

                        <div id="gameinfo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="gameinfoH">
                            <div class="panel-body">
                                Mode: {{ $game->mode->name }}<br/>
                                Mode-Score: {{$game->mode->score}}<br/>
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
                            </div>
                        </div>
                    </div>
                </div>
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

                        var finishes = {!! json_encode($finishes) !!};

                        var Game = function () {
                            var self = this;
                            this.players = [];
                            this.states = [new Playing(this)];        // TODO: get out of backend

                            var gameinfo = {!! json_encode($game) !!};
                            var stateInfo = {!! json_encode($game->getCurrentStateOfAllPlayer()) !!};
                            var scoreInfo = {!! json_encode($game->getCurrentPointsOfAllPlayer()) !!};

                            gameinfo.users.forEach(function (element, index, array) {

                                var player = new Player(element, self.states);
                                player.currentStateId = stateInfo[player.id].id;
//                                player.setState(player.currentStateId);
                                self.players.push(player);

                                // TODO: set State
                            });

                            for (var playerId in scoreInfo) {
                                var player = self.players.find(function (player) {
                                    return player.id == playerId;
                                });
                                player.points = scoreInfo[playerId];
                            }

                            this.setCurrentPlayer = function (playerObject) {
//                                console.log("ob", playerObject)
                                var currentPlayer = self.players.find(function (player) {
                                    return player.id == playerObject.id;
                                });

                                self.currentPlayer = currentPlayer;
                            };

                            this.gameOver = function () {
                                // set gameOver
                                // TODO: needed?
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
                            this.currentStateId = 0;

                            this.setState = function (id) {
                                this.states.forEach(function (element, index, array) {
                                    if (element.id == id) {
                                        self.currentState = element;
                                        self.currentStateId = id;
                                        //TODO: add break-mechanism
                                    }
                                });
                            }
                        }

                        var DoubleIn = function (game) {
                            this.game = game;
                            this.name = "DoubleIn";
                            this.id = 1; //TODO: set with db

                            this.handleInput = function (el) {

                                var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);

                                if (points.length < 3) {
                                    switch (scoreParameters[0]) {
                                        case "s":
                                            points.push([0, singleMultiplier]);
                                            break;
                                        case "d":
                                            points.push([scoreParameters[1], doubleMultiplier]);
                                            game.currentPlayer.setState(2);
                                            break;
                                        case "t":
                                            points.push([0, trippleMultiplier]);
                                            break;
                                        case "Bull":
                                            points.push([bullseye, singleMultiplier]);
                                            game.currentPlayer.setState(2);
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
                            this.name = "DoubleOut";
                            this.id = 2; // TODO: set with db

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

                        var _sumOfPoints = function (array) {
                            var sum = 0;

                            _.forEach(array, function (value) {
                                sum += value[0] * value[1];
                            })

                            return sum;
                        }

                        var Playing = function (game) {
                            this.game = game;
                            this.name = "Playing";
                            this.id = 3; // TODO: set with db

                            this.handleInput = function (el) {
                                var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);
                                var finished = false;

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

                                    // TODO: check if points reached 170 (area of finishing)

                                    // TODO: check if points reached 0 (win)
                                    if (game.currentPlayer.points - _sumOfPoints(points) == 0) {
                                        finished = true;
                                    }

                                    // TODO: check if overthrown
                                    if (game.currentPlayer.points - _sumOfPoints(points) < 0) {
                                        _.forEach(points, function (value) {
                                            value[0] = 0;
                                        });
                                        finished = true;
                                        // TODO: mark foul
                                    }

                                    updateGui(el);
                                }

                                if (points.length == 3 || finished) {
                                    button.prop('disabled', false);
                                }
                            }
                        }

                        var game = new Game();

                        var currentPlayer = {!! json_encode($game->getCurrentPlayer()) !!};
                        game.setCurrentPlayer(currentPlayer);

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

                            {{--$.ajax({--}}
                                {{--type: "POST",--}}
                                {{--url: '{{ route('store-state', $game->id) }}',--}}
                                {{--data: {--}}
                                    {{--_token: csrf_token,--}}
                                    {{--user: game.currentPlayer.id,--}}
                                    {{--game: "{{ $game->id }}",--}}
                                    {{--state_id: game.currentPlayer.currentStateId,  //TODO: change... obviously--}}
                                {{--},--}}
                                {{--success: function (response) {--}}
                                    {{--updatePlayerStates();--}}
                                {{--},--}}
                                {{--dataType: 'json'--}}
                            {{--});--}}

                            $.ajax({
                                type: "POST",
                                url: '{{ route('store-points', $game->id) }}',
                                data: {
                                    _token: csrf_token,
                                    user: game.currentPlayer.id,
                                    leg: "{{ $currentLeg->id }}",   // TODO: currentLeg muss noch manuell geupdatet werden!
                                    game: "{{$game->id}}",
                                    points: data,
                                },
                                success: function (response) {
                                    // TODO: auslagern?
                                    var playerPoints = JSON.parse(response)['playerPoints'];

                                    for (var playerId in playerPoints) {
                                        var player = game.players.find(function (player) {
                                            return player.id == playerId;
                                        });
                                        player.points = playerPoints[playerId];
                                    }

                                    // TODO: there must be a better solution than this....
                                    if (JSON.parse(response)['gameWon']) {
                                        window.location.reload();
                                    }

                                    if (JSON.parse(response)['legWon']) {
                                        window.location.reload();
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

                        function updatePlayerStates() {
                            game.players.forEach(function (element, index, array) {
                                var field = scoreBoard.find('#state-' + element.id);
                                field.text("Current State: " + element.currentState.name);
                            });
                        }

                        function addPointsElement(score) {
                            // TODO: dont append the score out the element clicked but the value evaluated by the state. e.g. DoubleIn->1 => show 0
                            currentScoreElement.append('<div class="col-xs-4">'
                                    + score + '<span class="glyphicon glyphicon-trash"></span></div>');
                            var lastScoreElement = currentScoreElement.find('div').last();
                            updateFinishElement(score);

                            lastScoreElement.find('span').click(function () {
                                currentScore = currentScore - score;
                                points.splice(currentScoreElement.find('div').index($(this).parent()), 1);
                                updateScoreElement(currentScore);
                                lastScoreElement.remove();
                                updateFinishElement(-score);
                                if (points.length < 3) {
                                    button.prop('disabled', true);
                                }
                            });
                        }

                        function updateScoreElement(score) {
                            playerScoreElement.text(score);
                        }

                        function updateFinishElement(score) {
                            var p = game.currentPlayer.points;
                            var finishElement = $('#finish-' + game.currentPlayer.id);
                            var h = p - _sumOfPoints(points);
                            if (h < 171) {
                                finishElement.text('Finish: ' + finishes[h]);
                            }


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