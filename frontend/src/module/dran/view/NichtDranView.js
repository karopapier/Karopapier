const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../templates/nichtdran.html'),
    tagName: 'tr',
});

