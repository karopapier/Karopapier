const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'chatLayout',
    template: require('../../../../templates/chat/chatLayout.html'),
    regions: {
        messages: '.chat-messages-container',
        chatInfo: '#chatInfo',
        chatControl: '#chatControl',
        chatEnter: '#chatEnter',
        webNotifier: '#webNotifier',
    },
});
