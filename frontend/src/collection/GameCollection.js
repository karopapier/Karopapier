const Backbone = require('backbone');
const Game = require('../model/Game');
module.exports = Backbone.Collection.extend({
    model: Game,
});

