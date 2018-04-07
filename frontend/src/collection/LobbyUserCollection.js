const Backbone = require('backbone');
module.exports = Backbone.Collection.extend({
    model: require('../model/newgame/LobbyUser'),

    comparator(model1, model2) {
        const a = model1.get('login').substr(0, 1).toLowerCase();
        const b = model2.get('login').substr(0, 1).toLowerCase();
        if (a < b) return -1;
        if (a === b) return 0;
        return 1;
    }
});

