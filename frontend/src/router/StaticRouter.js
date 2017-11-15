var Backbone = require('backbone');

module.exports = Backbone.Router.extend({
    initialize: function(options) {
        this.app = options.app
    },

    routes: {
        "*href": "showIndex",
    },

    showIndex: function(href) {
        this.app.navigate(href);
    }
});