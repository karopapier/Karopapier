const Marionette = require('backbone.marionette');
const UserlistItemView = require('./UserlistItemView');
module.exports = Marionette.CollectionView.extend({
    tagName: 'ul',
    childView: UserlistItemView,
});

