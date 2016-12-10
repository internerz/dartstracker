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
                                // aktueller Spieler muss nach der Reihenfolge gesetzt werden
                                self.currentPlayer = player;
                            };

                            this.gameOver = function() {
                                // set gameOver
                            }

                            console.log("what to do", this.currentPlayer, this.currentPlayer.currentState);

                            this.setCurrentPlayer(this.players[0]);

                            console.log("what to do", this.currentPlayer, this.currentPlayer.currentState);

                            // handle Input
                            // irgendwie path.click -> currentPlayer.currentState.handleInput()
                        }

                        var Player = function(player, states){
                            var self = this;
                            this.id = player.id;
                            this.name = player.name;
                            this.states = states;       // überflüssig? einfach game.states?
                            this.statesIndex = 0;
                            this.currentState = this.states[self.statesIndex];

                            this.nextState = function() {
                                self.statesIndex = self.statesIndex++;
                                self.currentState = self.states[self.statesIndex];
                            }
                        }

                        var DoubleIn = function(game){
                            this.game = game;

                            this.handleInput = function() {
                                // add the points according to the state

                                // certain condition -> player.nextState()
                            }
                        }

                        var DoubleOut = function(game) {
                            this.game = game;

                            this.handleInput = function() {
                                // add the points according to the state

                                // certain condition -> player.nextState()
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
                        //console.log("game_info: ", game);
                        var players = [];

                        gameInfo.users.forEach(function(element, index, array){
                            players.push(element);
                        });

                        console.log(players);


                        var game = new Game(players);

                        console.log(game);


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
                                    user: player.id,
                                    leg: "{{ $currentLeg->id }}",
                                    points: data,
                                },
                                success: function (response) {
                                    player = {
                                        id: JSON.parse(response)['nextPlayerId'],
                                        name: JSON.parse(response)['nextPlayerName'],
                                        points: JSON.parse(response)['playerPoints']
                                    };

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










































                        // update functions
                        function updatePlayerStrings() {
                            playerNameElement.text(player.name);
                        }

                        function updatePlayerPoints() {

                            for(var key in player.points) {
                                var field = scoreBoard.find('#id-' + key);
                                field.text(player.points[key]);
                            }
                        }

                        function updateScore(el) {
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

                                addPointsElement(getScorePoints(el));
                                currentScore = currentScore + getScorePoints(el);
                                updateScoreElement(currentScore);
                            }

                            if (points.length == 3) {
                                button.prop('disabled', false);
                            }
                        }

                        function addPointsElement(score) {
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

                        board.find("#areas g").children().hover(
                                function () {
                                    $(this).css("opacity", "0.5").css('cursor', 'pointer');
                                },
                                function () {
                                    $(this).css("opacity", "1");
                                }
                        ).click(function () {
                            updateScore($(this));
                        });

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