var Marionette = require('backbone.marionette');
var UserlistItemView = require('./UserlistItemView');
module.exports = Marionette.CollectionView.extend({
    tagName: "ul",
    childView: UserlistItemView
});

