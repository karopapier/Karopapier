// const _ = require('underscore');
const $ = require('jquery');
const Marionette = require('backbone.marionette');
const ChatLayout = require('../layout/ChatLayout');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const promiseChannel = Radio.channel('promise');
const ChatMessagesView = require('../view/ChatMessagesView');
const ChatEnterView = require('../view/ChatEnterView');

// const ChatMessageCache = require('../collection/ChatMessageCache');
// const ChatAppView = require('../view/chat/ChatAppView');
// const ChatInfoView = require('../view/chat/ChatInfoView');
// const ChatControlView = require('../view/chat/ChatControlView');

module.exports = Marionette.Application.extend({
    initialize() {
        this.layout = new ChatLayout({});
        this.chatMessages = dataChannel.request('chatMessages');

        promiseChannel.reply('send:message', (text) => {
            return new Promise((resolve, reject) => {
                if (text.trim === '' || !text) {
                    return reject(new Error('No message'));
                }
                const msg = {msg: text};
                $.ajax({
                    url: '/api/chat/message.json',
                    type: 'POST',
                    method: 'POST',
                    crossDomain: true,
                    // better than data: "msg=" + msg as it works with ???? as well
                    contentType: 'application/json',
                    data: JSON.stringify(msg),
                    xhrFields: {
                        withCredentials: true,
                    },
                    success: (data) => {
                        return resolve(data);
                    },
                    error: (xhr, status) => {
                        return reject(status, xhr);
                    },
                });
            });
        });

        this.start();
    },

    start() {
        this.layout.showChildView('messages', new ChatMessagesView({
            collection: this.chatMessages,
        }));

        this.layout.showChildView('chat-enter', new ChatEnterView());
    },
});

