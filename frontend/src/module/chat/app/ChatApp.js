// const _ = require('underscore');
const $ = require('jquery');
const Marionette = require('backbone.marionette');
const ChatLayout = require('../layout/ChatLayout');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const promiseChannel = Radio.channel('promise');

// model
const ChatSettings = require('../model/ChatSettings');

// view
const ChatMessagesView = require('../view/ChatMessagesView');
const ChatInfoView = require('../view/ChatInfoView');
const ChatControlView = require('../view/ChatControlView');
const ChatEnterView = require('../view/ChatEnterView');

// const ChatMessageCache = require('../collection/ChatMessageCache');
// const ChatAppView = require('../view/chat/ChatAppView');

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
                        this.chatMessages.fetchLatest();
                        return resolve(data);
                    },
                    error: (xhr, status) => {
                        return reject(status, xhr);
                    },
                });
            });
        });

        this.settings = new ChatSettings();
        this.users = dataChannel.request('users');

        this.start();
    },

    start() {
        // fetch 100 messages once at start
        this.chatMessages.fetch({data: {limit: 100}});

        // for the refresh, reduce to 10
        setInterval(() => {
            this.chatMessages.fetch({data: {limit: 10}, remove: false, merge: true});
        }, 60000);

        this.layout.showChildView('messages', new ChatMessagesView({
            model: this.settings,
            collection: this.chatMessages,
        }));

        this.layout.showChildView('chat-control', new ChatControlView({
            model: this.settings,
            collection: this.chatMessages,
        }));
        this.layout.showChildView('chat-enter', new ChatEnterView());

        // regularily fetch chat users and set the flag on the users accordingly
        setInterval(this.updateChatUsers, 60000);

        this.users.getLoadedPromise().then(() => {
            this.updateChatUsers().then(() => {
                this.layout.showChildView('chat-info', new ChatInfoView());
            });
        });
    },

    /**
     * pulls list of chat users and (re)sets chat flag accordingly
     * Sets all attributes given from API response, chat related or not...
     */
    updateChatUsers() {
        return new Promise((resolve) => {
            const url = 'api/chat/users.json';

            $.getJSON(url, false, (data) => {
                // negative list, who is already in?
                const currentChatUsers = this.users.where({'chat': true});

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
                    this.users.add(userData, {merge: true});
                }
                resolve();
            });
        });
    },
});

