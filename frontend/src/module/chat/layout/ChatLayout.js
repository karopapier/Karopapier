const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'chatLayout',
    template: require('../../../../templates/chat/chat-layout.html'),
    regions: {
        'messages': '.chat-messages-container',
        'chat-info': '.chat-info-container',
        'chatControl': '#chatControl',
        'chat-enter': '.chat-enter',
        'webNotifier': '#webNotifier',
    },
});
