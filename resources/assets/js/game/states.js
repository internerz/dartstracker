var SingleIn = function (game) {
    this.game = game;
    this.name = "SingleIn";
    this.phase = "Start";
    this.id = 6;

    this.setGame = function (game) {
        this.game = game;
    };

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
    };

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
};

var DoubleOut = function (game) {
    this.game = game;
    this.name = "DoubleOut";
    this.phase = "End";
    this.id = 2; // TODO: set with db

    this.setGame = function (game) {
        this.game = game;
    };

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
};

var Playing = function (game) {
    this.game = game;
    this.name = "Playing";
    this.phase = "Playing";
    this.id = 3; // TODO: set with db

    this.setGame = function (game) {
        this.game = game;
    };

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
};

var SingleOut = function (game) {
    this.game = game;
    this.name = "SingleOut";
    this.phase = "End";
    this.id = 7; // TODO: set with db

    this.setGame = function (game) {
        this.game = game;
    };

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
};

var possibleStates = [];
possibleStates.push(new SingleIn());
possibleStates.push(new DoubleIn());
possibleStates.push(new SingleOut());
possibleStates.push(new DoubleOut());
possibleStates.push(new Playing());