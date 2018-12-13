const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    tagName: 'span',
    template: require('../templates/notification-control.html'),

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },

    events: {
        'click': 'request',
    },

    request() {
        this.model.request();
    },
});
