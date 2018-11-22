const $ = window.jQuery = require('jquery');
const Backbone = require('backbone');
const Marionette = require('backbone.marionette');
const User = require('../model/User.js');
const DranGameCollection = require('../collection/DranGameCollection');
const KEvIn = require('../model/KEvIn');
const LocalSyncModel = require('../model/LocalSyncModel');
const KaroNotifier = require('../model/KaroNotifier');
const KaroNotifierView = require('../view/KaroNotifierView');
const NotificationControl = require('../model/NotificationControl');
const BrowserNotifier = require('../model/BrowserNotifier');
const KaroUtil = require('../model/Util');
const FaviconView = require('../view/FaviconView');
const TitleView = require('../view/TitleView');
const KaropapierLayout = require('../layout/KaropapierLayout');
const UserInfoBar = require('../view/UserInfoBar');
const NaviView = require('../view/NaviView');
const AppRouter = require('../router/AppRouter');
require('../polyfills');

const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

module.exports = Marionette.Application.extend(/** @lends KaropapierApp */ {
    // global layout with regions for nav, sidebar, header and user info...
    /**
     * @constructor KaropapierApp
     * @class KaropapierApp
     * @param options
     */
    initialize(options) {
        console.log('KAROPAPIER BBAPP INIT');

        if (!'realtimeHost' in options) {
            console.error('Need realtimeHost in options');
        }

        this.User = new User({});
        // make this user refer to "check" for loging in
        this.User.url = '/api/user/check';
        this.User.fetch();

        dataChannel.reply('logged:in:user', () => this.User);

        this.UserDranGames = new DranGameCollection({
            user: this.User,
        });

        dataChannel.reply('games:dran', () => this.UserDranGames);

        // init Karo Event Interface KEvIn
        this.KEvIn = new KEvIn({
            user: this.User,
            host: options.realtimeHost,
            vent: this.vent,
        });

        this.Settings = new LocalSyncModel({
            id: 1,
            storageId: 'settings',
            chat_funny: true,
            chat_limit: 20,
            chat_oldLink: false,
            notification_chat: true,
            notification_dran: true,
        });

        this.notifier = new KaroNotifier({
            eventEmitter: this.vent,
            user: this.User,
            settings: this.Settings,
        });
        this.notifierView = new KaroNotifierView({model: this.notifier});

        // Browser Notifications
        this.notificationControl = new NotificationControl();
        this.browserNotifier = new BrowserNotifier({
            eventEmitter: this.vent,
            user: this.User,
            settings: this.Settings,
            control: this.notificationControl,
        });

        this.util = KaroUtil;
        window.KaroUtil = KaroUtil;
        // lazy css
        KaroUtil.lazyCss('/css/slidercheckbox/slidercheckbox.css');

        this.listenTo(this, 'start', this.bootstrap.bind(this));
    },
    bootstrap() {
        let me = this;
        // console.log("Jetzt bootstrap app");

        // container for KaroNotifier
        this.notifierView.render();

        // hook to events to update dran queue
        // refresh function considering logout
        function dranRefresh() {
            if (me.User.get('id') <= 0) return false;
            me.UserDranGames.fetch();
        }

        dranRefresh();
        this.listenTo(this.User, 'change:id', dranRefresh);

        this.vent.on('USER:DRAN', (data) => {
            me.UserDranGames.addId(data.gid, data.name);
        });

        this.vent.on('USER:MOVED', (data) => {
            me.UserDranGames.remove(data.gid);
        });

        this.vent.on('message:new', (msg) => {
            let uc = me.User.get('uc');
            uc++;
            me.User.set('uc', uc);
        });

        // hook to events to update dran queue
        // refresh function considering logout
        function loadTheme() {
            if (me.User.get('id') == 0) return false;
            let theme = me.User.get('theme');
            let themeUrl = '/themes/' + theme + '/css/theme.css';
            KaroUtil.lazyCss(themeUrl);
        }

        loadTheme();
        this.listenTo(this.User, 'change:id', loadTheme);

        // init dynamic favicon
        this.favi = new FaviconView({
            model: this.User,
            el: '#favicon',
        });

        this.titler = new TitleView({
            model: this.User,
            title: 'Karopapier - Autofahren wie in der Vorlesung',
        });
        this.titler.render();

        // genereal page setup
        this.layout = new KaropapierLayout({
            el: 'body',
        });

        // user info bar right top
        this.infoBar = new UserInfoBar({
            model: this.User,
        });
        this.layout.header.show(this.infoBar);
        this.layout.navi.show(new NaviView());

        // Start the router
        this.router = new AppRouter({
            app: this,
        });
        Backbone.history.start({
            pushState: true,
            // root: '/i/'  // not on live branch wegen 2.server/
        });

        this.vent.on('GAME:MOVE', (data) => {
            // only for unrelated moves, count up or down
            if (data.related) return false;
            let movedUser = new User({id: data.movedId, login: data.movedLogin});
            movedUser.decreaseDran();
            let nextUser = new User({id: data.nextId, login: data.nextLogin});
            nextUser.increaseDran();
        });

        // global keyup handler for hotkeys
        $(document).on('keypress', (e) => {
            // if key pressed outside input, the target is "body", so we consider it a hotkey
            let targetTag = e.target.tagName.toUpperCase();
            if (targetTag === 'BODY') {
                if (e.which !== 0) {
                    // console.log("HOTKEY " + String.fromCharCode(e.which));
                    me.vent.trigger('HOTKEY', e);
                }
            }
        });
    },
});
