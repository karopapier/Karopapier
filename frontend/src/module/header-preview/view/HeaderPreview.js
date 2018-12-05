const Marionette = require('backbone.marionette');

const HeaderGameThumbnail = require('./HeaderGameThumbnail');

module.exports = Marionette.CollectionView.extend({
    className: 'header-dran-preview',
    childView: HeaderGameThumbnail,
});
