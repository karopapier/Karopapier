const Backbone = require('backbone');
module.exports = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    initialize() {
    },

    url() {
        return '/api/game/add.json';
    },
});
