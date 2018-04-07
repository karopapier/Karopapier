const Marionette = require('backbone.marionette');
module.exports = Marionette.CollectionView.extend({
    childView: require('./LobbyUserView'),

    initialize() {
        this.userFilter = this.getOption('modelFilter');
    }
});
