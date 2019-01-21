const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    template: require('../templates/navigation.html'),
    tagName: 'ul',
    className: 'top-nav medium-above-only',

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },
});
