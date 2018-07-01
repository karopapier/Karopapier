const Backbone = require('backbone');

module.exports = Backbone.Marionette.LayoutView.extend({
    template: require('../../templates/dran/dranLayout.html'),
    regions: {
        dranInfo: '#dranInfo',
        dranGames: '#dranGames',
    },
});
