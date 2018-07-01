const Backbone = require('backbone');
const ChatMessage = require('../model/ChatMessage');

module.exports = Backbone.Collection.extend({
    model: ChatMessage,
});
