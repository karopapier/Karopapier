// const _ = require('underscore');
const $ = require('jquery');
const Marionette = require('backbone.marionette');
const ChatLayout = require('../layout/ChatLayout');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const promiseChannel = Radio.channel('promise');
const ChatMessagesView = require('../view/ChatMessagesView');
const ChatInfoView = require('../view/ChatInfoView');
const ChatEnterView = require('../view/ChatEnterView');

// const ChatMessageCache = require('../collection/ChatMessageCache');
// const ChatAppView = require('../view/chat/ChatAppView');
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
                        this.chatMessages.fetch();
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
        this.layout.showChildView('chat-info', new ChatInfoView());

        // regularily fetch chat users and set the flag on the users accordingly
        setInterval(this.updateChatUsers, 60000);
        this.updateChatUsers();
    },

    /**
     * pulls list of chat users and (re)sets chat flag accordingly
     * Sets all attributes given from API response, chat related or not...
     */
    updateChatUsers() {
        const url = 'api/chat/users.json';

        $.getJSON(url, false, (data) => {
            const users = dataChannel.request('users');
            // negative list, who is already in?
            const currentChatUsers = users.where({'chat': true});

            const changeSet = {};
            // create changeset of all current chat users reset to false
            for (let i = 0; i < currentChatUsers.length; i++) {
                const login = currentChatUsers[i].get('login');
                const attribs = currentChatUsers[i].attributes;
                attribs.chat = false;
                changeSet[login] = attribs;
            }

            // chat flag will be set to true if still in list --> no change of chat property, no flicker
            for (let i = 0; i < data.length; i++) {
                const userData = data[i];
                userData.chat = true;

                const login = userData.login;
                changeSet[login] = userData;
            }

            for (const login in changeSet) {
                const userData = changeSet[login];
                users.add(userData, {merge: true});
            }
        });
    },
});

