// const _ = require('underscore');
// const $ = require('jquery');
const Marionette = require('backbone.marionette');
const ChatLayout = require('../layout/ChatLayout');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

// const ChatMessageCache = require('../collection/ChatMessageCache');
// const ChatAppView = require('../view/chat/ChatAppView');
// const ChatMessagesView = require('../view/chat/ChatMessagesView');
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
    },
});

