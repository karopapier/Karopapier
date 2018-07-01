const Backbone = require('backbone');
const Move = require('../model/Move');

module.exports = Backbone.Collection.extend(/** @lends MoveCollection.prototype */{
    model: Move,
});
