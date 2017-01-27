var bullseye = 50;
var outer = 25;
var singleMultiplier = 1;
var doubleMultiplier = 2;
var trippleMultiplier = 3;
var points = [];
var startingScore = 0;
var currentScore = startingScore;
var misthrow = 0;

var game, gameInfo, stateInfo, scoreInfo;
var button, board, currentScoreElement, playerNameElement, playerScoreElement, scoreBoard, overlay, currentPlayerNameElement, csrf_token;

var gameInfo = fromLocalStorage('gameInfo');
var stateInfo = fromLocalStorage('stateInfo');
var scoreInfo = fromLocalStorage('scoreInfo');

var Game = function () {
    var self = this;
    this.players = [];
    this.states = [];

    this.setupStates = function(states) {
        _.forEach(states, function (value) {
            possibleStates.forEach(function (element) {
                if (value.id == element.id) {
                    element.setGame(self);
                    self.states.push(element);
                }
            })
        });
    };

    this.setupPlayers = function () {
        gameInfo.users.forEach(function (element, index, array) {
            var player = new Player(element);
            player.currentStateId = stateInfo[player.id].id;
            player.setState(player.currentStateId);
            self.players.push(player);

            // TODO: set State
        });
    };

    this.setupPlayerPoints = function () {
        for (var playerId in scoreInfo) {
            var player = self.players.find(function (player) {
                return player.id == playerId;
            });
            player.points = scoreInfo[playerId];
        }
    };

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
};