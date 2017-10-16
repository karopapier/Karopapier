const Marionette = require('backbone.marionette');
const ContactView = require('./ContactView');
module.exports = Marionette.CollectionView.extend({
    childView: ContactView
});

