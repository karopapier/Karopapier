const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    className: 'newgame-layout',
    template: require('../../templates/newgame/newGameLayout.html'),
    regions: {
        name: '.newgame-name',
        playerfilter: '.playerfilter',
        playerlist: '.playerlist',
        selectedlist: '.selectedlist',
        mapcanvas: '.map-canvas',
        mapinfo: '.map-info',
    },

    triggers: {
        'input input': 'map:change',
    },
});
