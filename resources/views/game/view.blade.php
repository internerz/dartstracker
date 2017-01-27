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
            var finishes = {!! json_encode($finishes) !!};

            $(document).ready(function () {
                        button = $('#submit');
                        board = $('#board');
                        currentScoreElement = $('#currentScoreElement');
                        playerNameElement = $('#playerName');
                        playerScoreElement = $('#playerScore');
                        scoreBoard = $('#scoreBoard');
                        overlay = $('#overlay');
                        currentPlayerNameElement = $('currentPlayerNameElement');
                        csrf_token = $('meta[name="csrf-token"]').attr('content');

                        gameInfo = {!! json_encode($game) !!};
                        stateInfo = {!! json_encode($game->getCurrentStateOfAllPlayer()) !!};
                        scoreInfo = {!! json_encode($game->getCurrentPointsOfAllPlayer()) !!};

                        game = new Game();
                        game.setupStates({!! json_encode($game->states()->get()) !!});
                        game.setupPlayers();
                        game.setupPlayerPoints();
                        game.setCurrentPlayer({!! json_encode($game->getCurrentPlayer()) !!});

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
                    }
            );
        </script>
    @endif
@endsection