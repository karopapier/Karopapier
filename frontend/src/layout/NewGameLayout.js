const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../templates/newgame/newGameLayout.html'),
    regions: {
        playerfilter: '.playerfilter',
        playerlist: '.playerlist'
    }
});
