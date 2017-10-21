const Backbone = require('backbone');
const User = require('../model/User');
module.exports = Backbone.Collection.extend({
    model: User,
    url: '/api/users'
});

