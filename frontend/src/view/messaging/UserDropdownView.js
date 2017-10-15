var Marionette = require('backbone.marionette');
var UserOptionView = require('./UserOptionView');
module.exports = Marionette.CollectionView.extend({
    tagName: "select",
    childView: UserOptionView
});

