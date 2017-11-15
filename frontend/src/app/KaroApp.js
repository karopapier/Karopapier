'use strict';
const Radio = require('backbone.radio');
const Backbone = require('backbone');
const Marionette = require('backbone.marionette');

// Model
const User = require('../model/User');

const KEvIn = require('../model/KEvIn');

// View
const UserInfoBarView = require('../view/UserInfoBarView');

const PageLayout = Marionette.View.extend({
    template() {
        return;
    },
    regions: {
        userinfo: '#userInfoBar',
        content: '.content'
    }
});

module.exports = window.KaroApp = Marionette.Application.extend({
    region: '.container',

    initialize: function(config) {
        const me = this;
        this.config = config;
        console.info('App init');
        Backbone.emulateHTTP = true;

        this.availableApps = {
            messaging: require('./MessagingApp')
        };

        this.authUser = new User();
        this.authUser.url = '/api/users/check';
        this.authUser.fetch();

        this.dataProvider = Radio.channel('data');
        this.dataProvider.reply('user:logged:in', function() {
            return me.authUser;
        });

        this.dataProvider.reply('config', () => {
            return this.config;
        });

        this.kevin = new KEvIn();
    },

    switchApp(appname) {
        console.info('Switch to', appname);
        this.initApp(appname);
        this.layout.getRegion('content').detachView();
        this.layout.showChildView('content', this.apps[appname].layout);
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

    start: function() {
        console.info('App start');

        this.layout = new PageLayout({
            el: '.container'
        });

        this.apps = {};

        this.layout.showChildView('userinfo', new UserInfoBarView({
            model: this.authUser
        }));

        this.switchApp('messaging');
    },

    register: function() {
        this.navigator = Radio.channel('navigator');
    }
});
