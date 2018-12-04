const BaseCollection = require('../../../collection/BaseCollection');
const User = require('../model/User');
module.exports = BaseCollection.extend({
    model: User,
    url: '/api/users',
});

