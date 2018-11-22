const Backbone = require('backbone');

module.exports = Backbone.Router.extend({
    initialize(options) {
        this.app = options.app;
    },

    routes: {
        '*href': 'showIndex',
    },

    showIndex(href) {
        this.app.navigate(href);
    },
});
