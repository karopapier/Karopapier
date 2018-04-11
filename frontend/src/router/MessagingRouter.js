const Backbone = require('backbone');
module.exports = Backbone.Router.extend({
    initialize(options) {
        this.app = options.app;
    },

    routes: {
        'zettel/:contact': 'select',
        'zettel': 'index',
    },

    index: function() {
        this.app.unselect();
    },

    select: function(contactName) {
        this.app.selectName(contactName);
    },
});
