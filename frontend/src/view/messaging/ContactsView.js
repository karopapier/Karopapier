var Marionette = require('backbone.marionette');
var ContactView = require('./ContactView');
module.exports = Marionette.CollectionView.extend({
    childView: ContactView
});

