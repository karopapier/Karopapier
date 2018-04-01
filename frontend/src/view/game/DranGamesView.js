const Marionette = require('backbone.marionette');
const DranGameView = require('./DranGameView');
module.exports = Marionette.CollectionView.extend({
    tagName: 'tbody',
    childView: DranGameView
});

