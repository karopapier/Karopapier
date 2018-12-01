const ChatMessage = require('../model/ChatMessage');
const BaseCollection = require('../../../collection/BaseCollection');

module.exports = BaseCollection.extend({
    model: ChatMessage,
});
