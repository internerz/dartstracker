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
                    <div class="preview" id="preview"><span class="sdt">T</span><span class="number">20</span></div>
                    <div id="overlay"></div>
                    @include('game.dartboard')
                </div>
            </div>

            <div class="col-xs-12 col-md-5 col-lg-4">
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
                        var overlay = $('#overlay');

                        var startingScore = 0;
                        var currentScore = startingScore;
                        var possibleStates = [];

                        var bullseye = 50;
                        var outer = 25;
                        var singleMultiplier = 1;
                        var doubleMultiplier = 2;
                        var trippleMultiplier = 3;
                        var misthrow = 0;

                        var finishes = {!! json_encode($finishes) !!};

                        var Game = function () {
                            var self = this;
                            this.players = [];
                            //this.states = [new Playing(this)];        // TODO: get out of backend
                            this.states = [];
                            var states = {!! json_encode($game->states()->get()) !!};
                            _.forEach(states, function (value) {
                                possibleStates.forEach(function (element) {
                                    if (value.id == element.id) {
                                        element.setGame(self);
                                        self.states.push(element);
                                    }
                                })
                            })

                            var gameInfo = {!! json_encode($game) !!};
                            var stateInfo = {!! json_encode($game->getCurrentStateOfAllPlayer()) !!};
                            var scoreInfo = {!! json_encode($game->getCurrentPointsOfAllPlayer()) !!};


                            this.setupPlayers = function () {
                                gameInfo.users.forEach(function (element, index, array) {
                                    var player = new Player(element);
                                    player.currentStateId = stateInfo[player.id].id;
                                    player.setState(player.currentStateId);
                                    self.players.push(player);

                                    // TODO: set State
                                });
                            }

                            this.setupPlayerPoints = function () {
                                for (var playerId in scoreInfo) {
                                    var player = self.players.find(function (player) {
                                        return player.id == playerId;
                                    });
                                    player.points = scoreInfo[playerId];
                                }
                            }

                            this.setCurrentPlayer = function (playerObject) {
                                var currentPlayer = self.players.find(function (player) {
                                    return player.id == playerObject.id;
                                });

                                self.currentPlayer = currentPlayer;
                            };

                            // handle Input
                            this.handleInput = function (el) {
                                this.currentPlayer.currentState.handleInput(el);
                            }
                        }

                        var Player = function (player) {
                            var self = this;
                            this.id = player.id;
                            this.name = player.name;
                            this.points = 0;
                            this.currentState = null;
                            this.currentStateId = null;

                            this.setState = function (id) {
                                game.states.forEach(function (element, index, array) {
                                    if (element.id == id) {
                                        self.currentState = element;
                                        self.currentStateId = id;
                                    }
                                });
                            }

                            this.setStateByPhase = function (phase) {
                                game.states.forEach(function (element) {
                                    if (element.phase == phase) {
                                        self.currentState = element;
                                        self.currentStateId = element.id;
                                    }
                                })
                            }
                        }

                        var _sumOfPoints = function (array) {
                            var sum = 0;

                            _.forEach(array, function (value) {
                                sum += value[0] * value[1];
                            })

                            return sum;
                        }

                        var _sumOfPoints = function (array) {
                            var sum = 0;

                            _.forEach(array, function (value) {
                                sum += value[0] * value[1];
                            })

                            return sum;
                        }

                        var SingleIn = function (game) {
                            this.game = game;
                            this.name = "SingleIn";
                            this.phase = "Start";
                            this.id = 6;

                            this.setGame = function (game) {
                                this.game = game;
                            }

                            this.handleInput = function (el) {

                                var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);

                                if (points.length < 3) {
                                    switch (scoreParameters[0]) {
                                        case "s":
                                            points.push([scoreParameters[1], singleMultiplier]);
                                            this.game.currentPlayer.setStateByPhase("Playing");
                                            break;
                                        case "d":
                                            points.push([scoreParameters[1], doubleMultiplier]);
                                            this.game.currentPlayer.setStateByPhase("Playing");
                                            break;
                                        case "t":
                                            points.push([scoreParameters[1], trippleMultiplier]);
                                            this.game.currentPlayer.setStateByPhase("Playing");
                                            break;
                                        case "Bull":
                                            points.push([bullseye, singleMultiplier]);
                                            this.game.currentPlayer.setStateByPhase("Playing");
                                            break;
                                        case "Outer":
                                            points.push([outer, singleMultiplier]);
                                            this.game.currentPlayer.setStateByPhase("Playing");
                                            break;
                                        case "outer_ring":
                                            points.push([misthrow, singleMultiplier]);
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
                        };

                        var DoubleIn = function (game) {
                            this.game = game;
                            this.name = "DoubleIn";
                            this.phase = "Start";
                            this.id = 1; //TODO: set with db

                            this.setGame = function (game) {
                                this.game = game;
                            }

                            this.handleInput = function (el) {

                                var scoreParameters = el.attr('id').split(/(\d+)/).filter(Boolean);

                                if (points.length < 3) {
                                    switch (scoreParameters[0]) {
                                        case "s":
                                            points.push([0, singleMultiplier]);
                                            break;
                                        case "d":
                                            points.push([scoreParameters[1], doubleMultiplier]);
                                            this.game.currentPlayer.setStateByPhase("Playing");
                                            break;
                                        case "t":
                                            points.push([0, trippleMultiplier]);
                                            break;
                                        case "Bull":
                                            points.push([bullseye, singleMultiplier]);
                                            game.currentPlayer.setStateByPhase("Playing");
                                            break;
                                        case "Outer":
                                            points.push([0, singleMultiplier]);
                                            break;
                                        case "outer_ring":
                                            points.push([misthrow, singleMultiplier]);
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
                            this.phase = "End";
                            this.id = 2; // TODO: set with db

                            this.setGame = function (game) {
                                this.game = game;
                            }

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
                                        case "outer_ring":
                                            points.push([misthrow, singleMultiplier]);
                                            break;
                                        default:
                                            console.log("something bad happened");
                                    }

                                    if (this.game.currentPlayer.points - _sumOfPoints(points) == 0) {
                                        if (points[points.length - 1][1] == 2) {
                                            console.log("DoubleOut");
                                            finished = true;
                                        } else {
                                            _.forEach(points, function (value) {
                                                value[0] = 0;
                                            });
                                            finished = true;
                                            // TODO: mark foul --> set all score-elements to 0
                                        }
                                    } else if (this.game.currentPlayer.points - _sumOfPoints(points) < 0) {
                                        _.forEach(points, function (value) {
                                            value[0] = 0;
                                        });
                                        finished = true;
                                        // TODO: mark foul --> set all score-elements to 0
                                    }

                                    updateGui(el);
                                }

                                if (points.length == 3 || finished) {
                                    button.prop('disabled', false);
                                }
                            }
                        }

                        var Playing = function (game) {
                            this.game = game;
                            this.name = "Playing";
                            this.phase = "Playing";
                            this.id = 3; // TODO: set with db

                            this.setGame = function (game) {
                                this.game = game;
                            }

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
                                        case "outer_ring":
                                            points.push([misthrow, singleMultiplier]);
                                            break;
                                        default:
                                            console.log("something bad happened");
                                    }

                                    // TODO: check if points reached 170 (area of finishing)

                                    if (this.game.currentPlayer.points - _sumOfPoints(points) < 171) {
                                        this.game.currentPlayer.setStateByPhase("End");
                                    }

                                    // TODO: check if points reached 0 (win)

                                    if (this.game.currentPlayer.points - _sumOfPoints(points) == 0) {
                                        finished = true;
                                    }

                                    // TODO: check if overthrown
                                    if (this.game.currentPlayer.points - _sumOfPoints(points) < 0) {
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

                        var SingleOut = function (game) {
                            this.game = game;
                            this.name = "SingleOut";
                            this.phase = "End";
                            this.id = 7; // TODO: set with db

                            this.setGame = function (game) {
                                this.game = game;
                            }

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
                                        case "outer_ring":
                                            points.push([misthrow, singleMultiplier]);
                                            break;
                                        default:
                                            console.log("something bad happened");
                                    }

                                    // TODO: check if points reached 0 (win)

                                    if (this.game.currentPlayer.points - _sumOfPoints(points) == 0) {
                                        finished = true;
                                    }

                                    // TODO: check if overthrown
                                    if (this.game.currentPlayer.points - _sumOfPoints(points) < 0) {
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

                        possibleStates.push(new SingleIn());
                        possibleStates.push(new DoubleIn());
                        possibleStates.push(new SingleOut());
                        possibleStates.push(new DoubleOut());
                        possibleStates.push(new Playing());
                        var game = new Game();
                        game.setupPlayers();
                        game.setupPlayerPoints();

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

                        // Touch moving function
                        var lastTarget, target;
                        var preview = $('#preview');
                        board.find("#areas")
                                .on('touchstart', function () {
                                    $('body').addClass('touching');
                                })
                                .on('touchstart touchmove', function (e) {
                                    var pos = e.originalEvent.changedTouches[0];
                                    target = document.elementFromPoint(pos.clientX, pos.clientY);

                                    if (($(target).is('path') || $(target).is('circle')) && $(target).parent().attr('id') != 'score') {
                                        if (lastTarget != null) {
                                            if (target != lastTarget) {
                                                lastTarget.css('opacity', 1);
                                                preview.hide();
                                            }
                                        }

                                        $(target).css('opacity', 0.5);
                                        preview.show();

                                        var scoreParameters = getScoreParameters($(target));

                                        if (scoreParameters.length > 1) {
                                            preview.find('.sdt').text(scoreParameters[0].toUpperCase()).attr('class', 'sdt').addClass(scoreParameters[0]);
                                            preview.find('.number').text(scoreParameters[1]);
                                        } else {
                                            preview.find('.sdt').text('').attr('class', 'sdt').addClass(scoreParameters[0].toLowerCase());
                                            preview.find('.number').text(scoreParameters[0]);
                                        }

                                        lastTarget = $(target);
                                    } else {
                                        lastTarget.css('opacity', 1);
                                        preview.hide();
                                    }
                                })
                                .on('touchend', function () {
                                    lastTarget.css('opacity', 1);
                                    preview.hide();
                                    $('body').removeClass('touching');

                                    if ($(target).parent().parent().attr('id') == 'areas') {
                                        game.handleInput(lastTarget);
                                    }
                                });

                        button.click(function () {
                            var data = JSON.stringify(points);
                            button.prop('disabled', true);
                            points = [];

                            overlay.addClass('inactive');

                            $.ajax({
                                type: "POST",
                                url: '{{ route('store-state', $game->id) }}',
                                data: {
                                    _token: csrf_token,
                                    user: game.currentPlayer.id,
                                    game: "{{ $game->id }}",
                                    state_id: game.currentPlayer.currentStateId,  //TODO: change... obviously
                                },
                                success: function (response) {
                                    updatePlayerStates();
                                },
                                dataType: 'json'
                            });

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
                                    overlay.removeClass('inactive');

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

                                    currentScore = 0;
                                    removePointElements();
                                    updateScoreElement(startingScore);
                                    updatePlayerStrings();
                                    updatePlayerPoints();

                                },
                                dataType: 'json'
                            });
                            return false;
                        });

                        function updateGui(el) {
                            currentScore = currentScore + getScorePoints(el);
                            addPointsElement(getScorePoints(el));
                            updateScoreElement(currentScore);
                        }

                        function updatePlayerStrings() {
                            playerNameElement.text(game.currentPlayer.name);
                        }

                        function updatePlayerPoints() {
                            game.players.forEach(function (element, index, array) {
                                var field = scoreBoard.find('#id-' + element.id);
                                field.text(element.points);
                                if (element == game.currentPlayer) {
                                    field.text(element.points - currentScore);
                                }
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
                                updatePlayerPoints();
                            });

                            updatePlayerPoints();
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
                            var scoreParameters = getScoreParameters(el);

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
                                case "outer_ring":
                                    scoredPoints = misthrow;
                                    break;
                                default:
                                    console.log("something happened");
                            }

                            return scoredPoints;
                        }

                        function getScoreParameters(el) {
                            return el.attr('id').split(/(\d+)/).filter(Boolean);
                        }
                    }
            );
        </script>
    @endif
@endsection