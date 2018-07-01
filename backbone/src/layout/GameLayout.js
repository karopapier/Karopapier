const Marionette = require('backbone.marionette');

module.exports = Marionette.LayoutView.extend({
    template: require('../../templates/game/gameLayout.html'),
    regions: {
        gameQueue: '#gameQueue',
        gameInfo: '#gameInfo',
        gameTitle: '#gameTitle',
        gameStatus: '#gameStatus',
    },
});
