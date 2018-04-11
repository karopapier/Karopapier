const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/dran/nichtdran.html'),
    tagName: 'tr',
});

