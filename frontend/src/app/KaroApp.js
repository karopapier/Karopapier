'use strict';
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const Backbone = require('backbone');
const Marionette = require('backbone.marionette');

// channels
const appChannel = Radio.channel('app');
const layoutChannel = Radio.channel('layout');

// Model
const User = require('../model/User');
const LocalSyncModel = require('../model/LocalSyncModel');

// Collection
const UserCollection = require('../collection/UserCollection');
const GameCollection = require('../collection/GameCollection');
const MapCollection = require('../collection/MapCollection');

const KEvIn = require('../model/KEvIn');

// Router
const AppRouter = require('./../router/StaticRouter');

// Layout
const PageLayout = require('../layout/PageLayout');

// View
const UserInfoBarView = require('../view/UserInfoBarView');

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

        this.authUser = new User();
        this.authUser.url = '/api/users/check';
        this.authUser.fetch();

        this.users = new UserCollection();
        this.users.url = '/api/users';
        this.users.fetch();

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

        this.dranGames = new GameCollection();

        this.navigator = Radio.channel('navigator');
        dataChannel.reply('users', () => {
            return this.users;
        });

        dataChannel.reply('maps', () => {
            return this.maps;
        });

        dataChannel.reply('user:logged:in', () => {
            return this.authUser;
        });

        dataChannel.reply('config', () => {
            return this.config;
        });

        dataChannel.reply('settings', () => {
            return this.settings;
        });

        dataChannel.reply('users', () => {
            return this.users;
        });

        dataChannel.reply('drangames', () => {
            return this.dranGames;
        });

        layoutChannel.reply('region:modal', () => {
            return this.layout.getRegion('modal');
        });

        this.kevin = new KEvIn();

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
        console.log('Showing app\'s layout');
        this.layout.showChildView('content', this.apps[appname].layout);
        console.log('Showing apps layout done');
        this.currentApp = appname;
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

        this.dranGames.url = '/api/user/' + this.authUser.get('id') + '/dran';
        this.dranGames.fetch();

        // handle realtime updates of dranGames
        appChannel.on('user:moved', (data) => {
            const gid = data.gid;
            console.log('HAve to remove from dran', data);
            this.dranGames.remove(gid);
        });

        appChannel.on('user:dran', (data) => {
            console.log('HAve to add to dran', data);
            const g = {
                id: data.gid,
                name: data.name,
                dranName: data.nextLogin,
                blocked: new Date().getHours() + ':' + new Date().getMinutes(),
            };
            this.dranGames.add(g);
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

        console.log('Navigate', nav, data);

        this.switchNav(nav);

        // setz die URL im Browser ohne event
        Backbone.history.navigate(url, {
            trigger: false,
        });

        this.navigator.trigger(nav, data);
    },
});
