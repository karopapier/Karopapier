const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'chatLayout flex-item-column',
    template: require('../templates/chat-layout.html'),
    events: {
        'click .chat-mobile-info-switch': 'toggleInfoMode',
    },

    regions: {
        'messages': {
            el: '.chat-messages-container',
            replaceElement: true,
        },
        'chat-info': '.chat-info-container',
        'chat-control': '.chat-control',
        'chat-enter': '.chat-enter',
        'webNotifier': '#webNotifier',
    },

    toggleInfoMode() {
        this.$el.toggleClass('chat-info-mode');
    },
});
