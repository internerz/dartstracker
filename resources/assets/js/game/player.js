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
    };

    this.setStateByPhase = function (phase) {
        game.states.forEach(function (element) {
            if (element.phase == phase) {
                self.currentState = element;
                self.currentStateId = element.id;
            }
        })
    }
};