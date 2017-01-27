function toLocalStorage(name, item) {
    localStorage.setItem(name, JSON.stringify(item));
}

function fromLocalStorage(name) {
    return JSON.parse(localStorage.getItem(name));
}

function updateGui(el) {
    currentScore = currentScore + getScorePoints(el);
    addPointsElement(getScorePoints(el));
    updateScoreElement(currentScore);
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

function getScoreParameters(el) {
    return el.attr('id').split(/(\d+)/).filter(Boolean);
}

function updateFinishElement() {
    var p = game.currentPlayer.points;
    var finishElement = $('#finish-' + game.currentPlayer.id);
    var h = p - _sumOfPoints(points);
    if (h < 171) {
        finishElement.text('Finish: ' + finishes[h]);
    }
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

var _sumOfPoints = function (array) {
    var sum = 0;

    _.forEach(array, function (value) {
        sum += value[0] * value[1];
    });

    return sum;
};

function initGui() {
    game.players.forEach(function (player, i, array) {
        updatePlayerName(player.id, player.name);
        scoreBoard.find('#id-' + i).text(scoreInfo[i]);
    });
    updatePlayerStrings();
    updatePlayerStates();
    $('body').removeClass('loading');
}

function updatePlayerName(id, name) {
    $('.playerName' + id).text(name);
}

function updatePlayerStrings() {
    playerNameElement.text(game.currentPlayer.name);
}

function updatePlayerStates() {
    game.players.forEach(function (element, index, array) {
        var field = scoreBoard.find('#state-' + element.id);
        field.text("Current State: " + element.currentState.name);
    });
}

function removePointElements() {
    currentScoreElement.find('div').remove();
}

$(document).ready(function () {
    // Touch moving function
    var lastTarget, target;
    var preview = $('#preview');
    board = $('#board');

    board.find("#areas")
        .on('touchstart', function () {
            $('body').addClass('touching');
        })
        .on('touchstart touchmove', function (e) {
            var pos = e.originalEvent.changedTouches[0];
            target = document.elementFromPoint(pos.clientX, pos.clientY);

            if (($(target).is('path') || $(target).is('circle')) && $(target).parent().attr('id') != 'score' && $(target).attr('id') != 'down_under') {
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
});