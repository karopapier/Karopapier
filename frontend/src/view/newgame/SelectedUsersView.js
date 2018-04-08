const Marionette = require('backbone.marionette');
module.exports = Marionette.CollectionView.extend({
    childView: require('./LobbyUserView'),

    initialize() {
        this.listenTo(this.collection, 'change:selected', this.render);
    },

    filter(model, index, collection) {
        return model.get('selected');
    }
});
