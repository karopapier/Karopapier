const Marionette = require('backbone.marionette');
const DranGameView = require('./DranGameView');
const NichtDranView = require('./NichtDranView');

module.exports = Marionette.CollectionView.extend({
    tagName: 'tbody',
    childView: DranGameView,
    emptyView: NichtDranView,
});

