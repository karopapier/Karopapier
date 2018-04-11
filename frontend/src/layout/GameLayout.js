const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    className: 'game-layout',
    template: require('../../templates/game/gameLayout.html'),
    regions: {
        title: '.game-title',
        mapcanvas: '.mapview',
    },
});
