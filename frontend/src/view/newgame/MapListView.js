const Marionette = require('backbone.marionette');

module.exports = Marionette.CollectionView.extend({
    childView: require('../map/MapThumbnailView'),

    initialize() {
        // this.userFilter = this.getOption('filterModel');
        // this.listenTo(this.userFilter, 'change:desperate change:login', this.render);
    },

    filter(model, index, collection) {
        return true;
    },
});
