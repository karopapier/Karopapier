const Backbone = require('backbone');
const User = require('../../../../../backbone/src/model/User');

module.exports = Backbone.Collection.extend({
    url: '/api/chat/users.json',
    model: User,
});
