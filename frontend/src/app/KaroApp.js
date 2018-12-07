'use strict';
const Radio = require('backbone.radio');
const Backbone = require('backbone');
const Marionette = require('backbone.marionette');

// channels
const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');
const layoutChannel = Radio.channel('layout');

// Model
const AuthUser = require('../model/AuthUser');
const LocalSyncModel = require('../model/LocalSyncModel');
const UserManager = require('../module/data-manager/manager/UserManager');

// Collection
const MapCollection = require('../collection/MapCollection');

// Data Manages
const BlockerManager = require('../module/data-manager/manager/BlockerManager');
const DranGamesManager = require('../module/data-manager/manager/DranGamesManager');

const ChatMessageCollection = require('../module/chat/collection/ChatMessageCollection');

const KEvIn = require('../model/KEvIn');
const Linkifier = require('../util/Linkifier');

// Router
const AppRouter = require('./../router/StaticRouter');

// Layout
const PageLayout = require('../layout/PageLayout');

// View
const UserInfoBarView = require('../view/UserInfoBarView');
const FooterView = require('../module/footer/view/FooterView');
const MobileNavView = require('../module/mobile-nav/view/MobileNavView');
const HeaderPreview = require('../module/header-preview/view/HeaderPreview');

module.exports = window.KaroApp = Marionette.Application.extend({
    region: '.container',

    initialize(config) {
        this.config = config;
        console.info('App init');
        Backbone.emulateHTTP = true;

        // routing
        this.nav2app = {
            zettel: 'messaging',
            spiele: 'game',
            dran: 'dran',
            dran2: 'dran',
            erstellen: 'newgame',
            chat: 'chat',
            chat3: 'chat',
        };

        this.availableApps = {
            messaging: require('./MessagingApp'),
            game: require('./GameApp'),
            dran: require('./DranApp'),
            newgame: require('./NewGameApp'),
            chat: require('../module/chat/app/ChatApp'),
        };

        this.authUser = new AuthUser();
        this.authUser.fetch();

        this.linkifier = new Linkifier();

        this.settings = new LocalSyncModel({
            id: 1,
            storageId: 'settings',
            chat_funny: true,
            chat_limit: 20,
            chat_oldLink: false,
            notification_chat: true,
            notification_dran: true,
        });

        this.maps = new MapCollection();
        this.maps.url = '/api/map/list.json?nocode=true';
        // this.maps.url = '/api/map/list.json';

        this.chatMessages = new ChatMessageCollection();
        this.chatMessages.fetchLatest();

        this.navigator = Radio.channel('navigator');

        dataChannel.reply('user:logged:in', () => {
            return this.authUser;
        });

        dataChannel.reply('maps', () => {
            return this.maps;
        });

        dataChannel.reply('config', () => {
            return this.config;
        });

        dataChannel.reply('settings', () => {
            return this.settings;
        });

        dataChannel.reply('chatMessages', () => {
            return this.chatMessages;
        });

        dataChannel.reply('linkifier', () => {
            return this.linkifier;
        });

        layoutChannel.reply('region:modal', () => {
            return this.layout.getRegion('modal');
        });

        this.kevin = new KEvIn();
        this.kevin.update();

        this.router = new AppRouter({
            app: this,
        });
    },

    switchNav(nav) {
        console.info('Switch to nav', nav);
        const app = this.nav2app[nav];
        this.switchApp(app);
    },

    switchApp(appname) {
        console.info('Switch to app', appname);
        this.initApp(appname);
        this.layout.getRegion('content').detachView();
        // console.log('Showing app\'s layout');
        this.layout.showChildView('content', this.apps[appname].layout);
        // console.log('Showing apps layout done');
        this.currentApp = appname;

        // fitscreen handling
        if (appname === 'chat') {
            document.getElementsByTagName('body')[0].classList.add('fitscreen');
        } else {
            document.getElementsByTagName('body')[0].classList.remove('fitscreen');
        }
    },

    initApp(appname) {
        if (appname in this.apps) {
            return;
        }

        this.apps[appname] = new this.availableApps[appname](this.config);
        // they need to start themselves
        // app.start();
    },

    start() {
        console.info('Karo App start');

        // start data managers
        // they init, load and update collections (interval & realtime) to keep data fresh
        new UserManager();
        new DranGamesManager();
        new BlockerManager();

        this.listenTo(appChannel, 'chat:message', (message) => {
            this.chatMessages.fetchLatest();
        });

        this.layout = new PageLayout({
            el: '.container',
        });
        this.layout.on('navigate', (href) => {
            this.navigate(href);
        });

        this.apps = {};

        this.layout.showChildView('userinfo', new UserInfoBarView({
            model: this.authUser,
        }));

        this.layout.showChildView('footer', new FooterView());
        this.layout.showChildView('mobile-nav', new MobileNavView());
        this.layout.showChildView('header-preview', new HeaderPreview({
            collection: dataChannel.request('dranGames'),
        }));

        Backbone.history.start({pushState: true});
    },

    navigate(href) {
        let url = href.replace(window.location.origin, '');
        if (url.substr(0, 1) === '/') {
            url = url.substr(1);
        }

        /**
         * Aktuelle nur URLs mit 1 oder 2 Teilen, also /mitteilungen und /mitteilungen/username
         * @type {Array|*}
         */

        const parts = url.split('/');
        const nav = parts.shift();
        const data = parts.shift();

        console.info('Navigate', nav, data);

        this.switchNav(nav);

        // setz die URL im Browser ohne event
        Backbone.history.navigate(url, {
            trigger: false,
        });

        this.navigator.trigger(nav, data);
    },
});
