// container for rendered map and players
const Marionette = require('backbone.marionette');
module.exports = Marionette.CompositeView.extend({
    childViewContainer: 'tbody',
    template: window['JST']['dran/dranGames'],
});
