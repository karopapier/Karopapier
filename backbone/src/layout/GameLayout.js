const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    template: require('../../templates/game/gameLayout.html'),
    regions: {
        gameQueue: '#gameQueue',
        gameInfo: '#gameInfo',
        gameTitle: '#gameTitle',
        gameStatus: '#gameStatus',
    },
});
