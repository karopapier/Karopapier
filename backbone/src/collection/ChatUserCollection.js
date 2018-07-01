const Backbone = require('backbone');
const User = require('../model/User');

module.exports = Backbone.Collection.extend({
    url: APIHOST + '/api/chat/users.json?callback=?',
    model: User,
});
