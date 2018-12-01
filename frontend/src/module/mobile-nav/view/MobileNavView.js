const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'mobile-nav mobile-only',
    template: require('../templates/mobile-nav.html'),
});
