const Marionette = require('backbone.marionette');

const HeaderGameThumbnail = require('./HeaderGameThumbnail');

module.exports = Marionette.NextCollectionView.extend({
    className: 'header-dran-preview',
    childView: HeaderGameThumbnail,

    initialize() {
        this.listenTo(this.collection, 'add remove', this.filter);
    },

    viewFilter(view, index) {
        // only show 20 max
        return index < 20;
    },
});
