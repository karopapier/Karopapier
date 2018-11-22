const Backbone = require('backbone');

module.exports = Backbone.Router.extend({
    routes: {
        'game.html?GID=:gameId': 'showGame',
        'newshowmap.php?GID=:gameId': 'showGame',
        'game.html': 'defaultRoute',
    },
    showGame(gameId) {
        if (gameId) {
            game.load(gameId);
        }
    },
    defaultRoute() {
        this.navigate('game.html', {trigger: true});
        // this.navigate("game.html?GID=81161", {trigger: true});
        // this.navigate("game.html?GID=57655", {trigger: true});
    },
});


