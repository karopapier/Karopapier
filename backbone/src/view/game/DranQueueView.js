// container for rendered map and players
const Marionette = require('backbone.marionette');
const DranQueueItemView = require('./DranQueueItemView');

module.exports = Marionette.CollectionView.extend({
    tagName: 'div',
    childView: DranQueueItemView,
});
