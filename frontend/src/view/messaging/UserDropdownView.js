const Marionette = require('backbone.marionette');
const UserOptionView = require('./UserOptionView');
module.exports = Marionette.CollectionView.extend({
    tagName: 'select',
    childView: UserOptionView
});

