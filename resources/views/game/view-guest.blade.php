@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="headline" id="players">
                    <span class="playerName0">Player 1</span> vs. <span class="playerName1">Player 2</span>
                </h2>
            </div>

            <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-7 col-md-offset-0 col-lg-8">
                <div class="points" id="points">
                    <div class="preview" id="preview"><span class="sdt">T</span><span class="number">20</span></div>
                    @include('game.dartboard')
                </div>
            </div>

            <div class="col-xs-12 col-md-5 col-lg-4">
                <div class="currentSore">
                    <h3 id="score" class="score">
                        <span id="playerName">Player 1</span> darts:
                        <span id="playerScore" class="playerScore">0</span>
                    </h3>
                    <div id="currentScoreElement" class="row currentScoreElement"></div>
                    <button type="submit" class="btn btn-primary" id="submit" disabled>Save Score</button>
                </div>

                <h3>Score</h3>

                <div class="row" id="scoreBoard">
                    <div class="col-md-6 col-xs-6">
                        <span class="playerName0">Player 1</span><br/>
                        <span class="score"
                              id="id-0">{{ $mode->score }}</span><br/>
                        <span id="state-0">Current State: <span class="state">STATE</span></span><br/>
                        <span id="finish-0">Finish: -</span>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <span class="playerName1">Player 2</span><br/>
                        <span class="score"
                              id="id-1">{{ $mode->score }}</span><br/>
                        <span id="state-1">Current State: <span class="state">STATE</span></span><br/>
                        <span id="finish-1">Finish: -</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
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

                    game = new Game();
                    game.setupStates({!! $states !!});
                    game.setupPlayers();
                    game.setupPlayerPoints();
                    game.setCurrentPlayer(gameInfo['currentPlayer']);

                    initGui();

                    button.click(function () {
                        var data = JSON.stringify(points);
                        button.prop('disabled', true);
                        points = [];

                        overlay.addClass('inactive');

                        updatePlayerStates();

                        data = JSON.parse(data);

                        var playerPoints = scoreInfo;
                        playerPoints[game.currentPlayer.id] = playerPoints[game.currentPlayer.id] - _sumOfPoints(data);

                        for (var playerId in playerPoints) {
                            var player = game.players.find(function (player) {
                                return player.id == playerId;
                            });
                            player.points = playerPoints[playerId];
                        }

                        //
//                        // TODO: there must be a better solution than this....
//                        if (JSON.parse(response)['gameWon']) {
//                            window.location.reload();
//                        }
//
//                        if (JSON.parse(response)['legWon']) {
//                            window.location.reload();
//                        }
//
                        var found = false;

                        game.players.forEach(function (player, index, array) {
                            if (player.id != game.currentPlayer.id && !found) {
                                game.setCurrentPlayer(player);
                                gameInfo['currentPlayer'] = {"id": player.id, "name": player.name};
                                found = true;
                            }
                        });

                        currentScore = 0;
                        removePointElements();
                        updateScoreElement(startingScore);
                        updatePlayerStrings();
                        updatePlayerPoints();

                        toLocalStorage('scoreInfo', playerPoints);
                        toLocalStorage('gameInfo', gameInfo);

                        return false;
                    });
                }
        );
    </script>
@endsection