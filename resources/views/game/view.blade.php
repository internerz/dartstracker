@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Game #{{ $game->id }}</div>

                    <div class="panel-body">
                        Mode: {{ $game->mode->name }}<br/>
                        Ruleset: {{ $game->ruleset }}<br/>
                        Number of legs to win: {{ $game->number_of_legs_to_win }}<br/>
                        Current leg: {{ $game->legs->count() }}<br/>
                        Current player: {{ $game->getCurrentPlayer()->name }}<br/>
                        Next player: {{ $game->getNextPlayer()->name }}<br/>
                        @if ($currentLeg)
                            Current leg ID: {{ $currentLeg->id }}<br/>
                        @endif
                        Players: @foreach ($game->users as $user)
                            {{ $user->name }},
                        @endforeach
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">Score</div>

                    <div class="panel-body" id="scoreBoard">
                        @foreach ($game->users as $user)
                            <div class="col-md-{{ 12/count($game->users) }}">
                                {{ $user->name }}<br />
                                <span class="score" id="id-{{$user->id}}">501 : {{$game->getCurrentPointsOfPlayer($user)}}</span>
                            </div>
                        @endforeach
                    </div>

                    @if ($currentLeg)
                        <div class="panel-body" id="points">
                            @include('game.dartboard')

                            <div class="row" id="currentScoreElement">
                                <h2 id="score" class="score col-sm-12">
                                    <span id="playerName">{{ $game->getCurrentPlayer()->name }}</span> darts:
                                    <span id="playerScore">0</span>
                                </h2>
                            </div>

                            <button type="submit" class="btn btn-primary" id="submit" disabled>Submit</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @if ($currentLeg)
        <script type="text/javascript">
            $(document).ready(function () {
                        var Game = function(players) {
                            var self = this;
                            this.players = [];
                            this.states = [new DoubleIn(this), new DoubleOut(this)];
                            players.forEach(function(element, index, array) {
                                self.players.push(new Player(element, self.states));
                            });

                            this.currentPlayer = this.players[1];

                            this.setCurrentPlayer = function(player) {
                                // TODO: aktueller Spieler muss nach der Reihenfolge gesetzt werden
                                self.currentPlayer = player;
                            };

                            this.gameOver = function() {
                                // set gameOver
                            }

//                            console.log("what to do", this.currentPlayer, this.currentPlayer.currentState);

                            this.setCurrentPlayer(this.players[0]);

//                            console.log("what to do", this.currentPlayer, this.currentPlayer.currentState);

                            // handle Input
                            this.handleInput = function(el) {
                                console.log(this.currentPlayer.currentState);
                                // gets Input and deals with it per currentplayer and the state he's currently in
                                this.currentPlayer.currentState.handleInput(el);
                            }
                        }

                        var Player = function(player, states){
                            var self = this;
                            this.id = player.id;
                            this.name = player.name;
                            this.points = 0;
                            this.states = states;       // überflüssig? einfach game.states?
                            this.statesIndex = 0;
                            this.currentState = this.states[self.statesIndex];

                            this.nextState = function() {
                                // TODO: not increment statesIndex but set states to a specific state --> no state ordering

                                self.statesIndex++;
                                self.currentState = self.states[self.statesIndex];
                                console.log("state change", self.statesIndex, self.currentState);
                            }
                        }

                        var DoubleIn = function(game){
                            this.game = game;

                            this.handleInput = function(el) {
//                                console.log("DoubleIn", el.attr('id'));

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
                                }

                                updateGui(el);

                                if (points.length == 3) {
                                    button.prop('disabled', false);
                                }
                            }
                        }

                        var DoubleOut = function(game) {
                            this.game = game;

                            this.handleInput = function(el) {
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
                                }

                                updateGui(el);

                                if (points.length == 3) {
                                    button.prop('disabled', false);
                                }
                            }
                        }

                        var Playing = function(game) {
                            this.game = game;

                            this.handleInput = function() {
                                // add the points according to the state

                                // certain condition -> player.nextState()
                            }
                        }

                        var gameInfo = {!! json_encode($game) !!};

                        var players = [];

                        gameInfo.users.forEach(function(element, index, array){
                            players.push(element);
                        });

                        var game = new Game(players);

                        var player = {
                            id: "{{ \Auth::user()->id }}",
                            name: "{{ \Auth::user()->name }}",
                            points: ""
                        };
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

                        button.click(function () {
                            var data = JSON.stringify(points);
                            button.prop('disabled', true);
                            points = [];

                            $.ajax({
                                type: "POST",
                                url: window.location.href.split('#')[0],
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
                                    var playerPoints = JSON.parse(response)['playerPoints'];

                                    for(var playerId in playerPoints) {
                                        var player = game.players.find(function(player){
                                           return player.id == playerId;
                                        });
                                        player.points = playerPoints[playerId];
                                    }

                                    game.players.forEach(function(element, index, array){
                                        if(element.id == JSON.parse(response)['nextPlayerId']){
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

                        function updateGui(el){
                            addPointsElement(getScorePoints(el));
                            currentScore = currentScore + getScorePoints(el);
                            updateScoreElement(currentScore);
                        }

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

                        // update functions
                        function updatePlayerStrings() {
                            playerNameElement.text(game.currentPlayer.name);
                        }

                        function updatePlayerPoints() {
                            game.players.forEach(function(element, index, array){
                                var field = scoreBoard.find('#id-' + element.id);
                                console.log(element.points);
                                field.text(element.points);
                            });
                        }

                        function addPointsElement(score) {
                            // TODO: implement in states
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

                        /*
                         some helper function(s), maybe not needed
                         */
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