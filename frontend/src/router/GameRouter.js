const Backbone = require('backbone');
module.exports = Backbone.Router.extend({
    initialize(options) {
        this.app = options.app;
    },

    routes: {
        'games/:gid': 'show',
        'spiele/:gid': 'show',
        'games': 'index',
        'spiele': 'index',
    },

    index() {
        this.app.unselect();
    },

    show(gid) {
        this.app.show(gid);
    },
});
