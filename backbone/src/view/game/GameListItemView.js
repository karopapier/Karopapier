// container for rendered map and players
const Marionette = require('backbone.marionette');

module.exports = Marionette.ItemView.extend({
    tagName: 'tr',
    template: require('../../../templates/game/gameListItem.html'),
});

