// const _ = require('underscore');
// const $ = require('jquery');
const Marionette = require('backbone.marionette');
const ChatLayout = require('../layout/ChatLayout');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const ChatMessagesView = require('../view/ChatMessagesView');

// const ChatMessageCache = require('../collection/ChatMessageCache');
// const ChatAppView = require('../view/chat/ChatAppView');
// const ChatInfoView = require('../view/chat/ChatInfoView');
// const ChatControlView = require('../view/chat/ChatControlView');
// const ChatEnterView = require('../view/chat/ChatEnterView');

module.exports = Marionette.Application.extend({
    initialize() {
        this.layout = new ChatLayout({});
        this.chatMessages = dataChannel.request('chatMessages');

        this.start();
    },

    start() {
        this.layout.showChildView('messages', new ChatMessagesView({
            collection: this.chatMessages,
        }));
    },
});

