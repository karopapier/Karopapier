const Backbone = require('backbone');
const Message = require('../model/Message');
module.exports = Backbone.Collection.extend({
    model: Message,
    comparator: 'ts',
    url: '/api/messages',
});

