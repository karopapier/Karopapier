// container for rendered map and players
const Marionette = require('backbone.marionette');

module.exports = Marionette.CompositeView.extend({
    childViewContainer: 'tbody',
    template: require('../../../templates/dran/dranGames.html'),
});
