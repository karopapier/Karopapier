const BaseCollection = require('./BaseCollection');
const User = require('../model/User');
module.exports = BaseCollection.extend({
    model: User,
    url: '/api/users'
});

