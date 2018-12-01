const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'chatLayout',
    template: require('../../../../templates/chat/chatLayout.html'),
    regions: {
        chatMessages: '#chatMessages',
        chatInfo: '#chatInfo',
        chatControl: '#chatControl',
        chatEnter: '#chatEnter',
        webNotifier: '#webNotifier',
    },
});
