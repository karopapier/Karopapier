/*
const _ = require('underscore');
const $ = require('jquery');
const Backbone = require('backbone');
const Marionette = require('backbone.marionette');
const ChatLayout = require('../../../../../backbone/src/layout/ChatLayout');
const ChatMessageCache = require('../../../frontend/src/module/chat/collection/ChatMessageCache');
const ChatMessageCollection = require('../collection/ChatMessageCollection');
const ChatAppView = require('../../../../../backbone/src/view/chat/ChatAppView');
const ChatMessagesView = require('../../../../../backbone/src/view/chat/ChatMessagesView');
const ChatInfoView = require('../../../../../backbone/src/view/chat/ChatInfoView');
const ChatControlView = require('../../../../../backbone/src/view/chat/ChatControlView');
const ChatEnterView = require('../../../../../backbone/src/view/chat/ChatEnterView');

module.exports = Marionette.Application.extend({
    initialize(options) {
        options = options || {};
        if (!options.app) {
            console.error('No app passed to ChatApp');
            return false;
        }
        if (!options.settings) {
            console.error('No settings passed to ChatApp');
            return false;
        }
        _.bindAll(this, 'updateView', 'start', 'scrollCheck');
        this.app = options.app;
        this.settings = options.settings;
        this.layout = new ChatLayout({});
        this.view = new ChatAppView({
            model: this,
        });
        this.already = true;

        // console.log("Im ChatApp.init ist funny", Karopapier.Settings.get("chat_funny"));
        this.configuration = new Backbone.Model({
            limit: this.settings.get('chat_limit'),
            lastLineId: 0,
            atEnd: true,
            start: 0,
            history: false,
            funny: this.settings.get('chat_funny'),
            showBotrix: this.settings.get('chat_showBotrix'),
            oldLink: this.settings.get('chat_oldLink'),
        });
        this.app.util.set('funny', this.configuration.get('funny'));
        this.app.util.set('oldLink', this.configuration.get('oldLink'));

        this.chatMessageCache = new ChatMessageCache({});
        this.chatMessageCache.cache(0, 20); // initial short load

        this.chatMessageCollection = new ChatMessageCollection({
            app: this.app,
        });

        this.chatMessagesView = new ChatMessagesView({
            model: this.configuration,
            collection: this.chatMessageCollection,
            util: this.app.util,
        });
        // this.chatMessagesView.render();

        this.chatInfoView = new ChatInfoView({
            model: this.app.User,
            app: this.app,
        });

        this.chatControlView = new ChatControlView({
            app: this.app,
            model: this.configuration,
        });

        this.chatEnterView = new ChatEnterView({
            model: this.app.User,
        });

        this.listenTo(this.configuration, 'change:limit', function(conf, limit) {
            if (this.configuration.get('atEnd')) {
                const start = this.configuration.get('lastLineId') - this.configuration.get('limit');
                this.configuration.set('start', start);
            }
            this.app.Settings.set('chat_limit', limit);
        });

        this.listenTo(this.configuration, 'change:start', function(conf, start) {
            console.log('Start changed, was ', conf.previous('start'), 'now', start);
            this.chatMessageCache.cache(start);
        });

        this.listenTo(this.configuration, 'change:showBotrix', function(conf, showBotrix) {
            this.app.Settings.set('chat_showBotrix', showBotrix);
        });

        this.listenTo(this.configuration, 'change:funny', function(conf, funny) {
            this.app.Settings.set('chat_funny', funny);
        });

        this.listenTo(this.configuration, 'change:oldLink', function(conf, oldLink) {
            this.app.Settings.set('chat_oldLink', oldLink);
        });

        this.listenTo(this.app.Settings, 'change:chat_limit', function(conf, limit) {
            this.configuration.set('limit', limit);
        });

        this.listenTo(this.app.Settings, 'change:chat_funny', function(conf, funny) {
            // console.log("ChatApp bekommt mit, dass sich Karo.Settings -> funny ge�ndert hat",funny);
            this.configuration.set('funny', funny);
            this.app.util.set('funny', funny);
            this.chatMessageCache.each((m) => {
                // dummy trigger change event to force re-render
                m.set('funny', funny);
            });
        });

        this.listenTo(this.app.Settings, 'change:chat_oldLink', function(conf, oldLink) {
            // console.log("ChatApp bekommt mit, dass sich Karo.Settings -> oldLink ge�ndert hat", oldLink);
            this.configuration.set('oldLink', oldLink);
            this.app.util.set('oldLink', oldLink);
            this.chatMessageCache.each((m) => {
                // dummy trigger change event to force re-render
                m.set('oldLink', oldLink);
            });
        });

        this.listenTo(this.app.Settings, 'change:chat_showBotrix', function(conf, showBotrix) {
            // console.log("ChatApp bekommt mit, dass sich Karo.Settings -> showBotrix ge�ndert hat",showBotrix);
            this.configuration.set('showBotrix', showBotrix);
            this.chatMessageCache.each((m) => {
                // dummy trigger change event to force re-render
                m.set('showBotrix', showBotrix);
            });
        });

        this.listenTo(this.chatMessageCache, 'CHAT:CACHE:UPDATED', function() {
            // chat cache was updated - filter what to view
            const start = this.configuration.get('start');
            const end = parseInt(start) + parseInt(this.configuration.get('limit'));
            const toShow = this.chatMessageCache.filter((cm) => {
                const lineId = cm.get('lineId');
                // console.log("Check",lineId,"to be between",start,end);
                return ((lineId >= start) && (lineId <= end));
            });
            // console.log("Between",start,"and",end,"lie",toShow.length);
            this.chatMessageCollection.set(toShow);
        });

        this.listenTo(this.chatMessagesView, 'CHAT:MESSAGES:TOP', function() {
            if (!this.configuration.get('history')) {
                console.info('Not in history mode');
                return false;
            }

            const extender = 100;
            let start = this.configuration.get('start');
            const limit = this.configuration.get('limit');
            if (start <= 1) return true;
            start -= extender;
            this.configuration.set({
                start,
                limit: limit + extender,
            });
            this.configuration.set('start', start);
        });

        // wire message cache and view collection together
        const me = this;
        this.listenTo(this.chatMessageCache.info, 'change:lastLineId', function(ll) {
            console.warn('Update conf ll to ', ll.get('lastLineId'));
            this.configuration.set('lastLineId', ll.get('lastLineId'));
        });

        this.listenTo(this.configuration, 'change:lastLineId', function() {
            // a change here only matters if we are "at the end"
            const ll = this.configuration.get('lastLineId');
            if (this.configuration.get('atEnd')) {
                const limit = this.configuration.get('limit');
                const start = ll - limit;
                // do this silently if start was 0
                this.configuration.set('start', start, {});
            }
        });

        // this.listenTo(this.chatMessageCache, "add", this.updateView);
        // this.listenTo(this.configuration, "change:limit", this.updateView);

        // dirty first poor man's refresh and backup
        this.refreshMessages = setInterval(() => {
            // this.chatMessageCollection.fetch();
            this.chatInfoView.updateTopBlocker();
            // keepalive
            $.getJSON('/api/chat/list.json?limit=2');
        }, 59000);

        this.app.vent.on('CHAT:MESSAGE', (data) => {
            // console.log("vent CHAT:MESSAGE triggered inside ChatApp");
            // disable due to XSS danger
            // console.log(data);
            // var cm = new ChatMessage(data.chatmsg);
            // console.log(cm);
            // me.chatMessageCollection.add(cm);
            me.chatMessageCache.cache(me.configuration.get('lastLineId'));
        });
    },
    updateView() {
        // console.log("updateView");
        if (this.configuration.get('atEnd')) {
            console.log('We are at the end');
            const l = this.chatMessageCache.length;
            const lim = this.configuration.get('limit');
            this.chatMessageCollection.set(this.chatMessageCache.slice(l - lim));
        }
    },
    scrollCheck(e) {
        // console.log("Check already", this.already);
        const cmv = this.chatMessagesView;
        const me = this;
        if (this.already) {
            cmv.scrollCheck();
            this.already = false;
            setTimeout(() => {
                me.already = true;
            }, 50);
        }
    },
    start() {
        // Karopapier.chatApp.layout); //, {preventDestroy: true});
        // console.log(Karopapier.chatApp.layout);
        // Karopapier.ca = new ChatLayout();
        // Karopapier.ca.chatInfo.show(new LogView());
        // Karopapier.chatApp.layout.render();
        // Karopapier.chatApp.start();
        // }


    },
});

*/
