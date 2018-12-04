const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'chatLayout',
    template: require('../templates/chat-layout.html'),
    regions: {
        'messages': '.chat-messages-container',
        'chat-info': '.chat-info-container',
        'chat-control': '.chat-control',
        'chat-enter': '.chat-enter',
        'webNotifier': '#webNotifier',
    },
});
