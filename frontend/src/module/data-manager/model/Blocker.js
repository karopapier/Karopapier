const Backbone = require('backbone');
module.exports = Backbone.Model.extend({
    defaults: {
        id: 0,
        login: '',
        activeGames: 0,
        dran: 0,
    },
});

