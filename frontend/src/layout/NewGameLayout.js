const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../templates/newgame/newGameLayout.html'),
    regions: {
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
