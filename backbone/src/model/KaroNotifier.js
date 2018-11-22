const _ = require('underscore');
const Backbone = require('backbone');
const BrowserNotification = require('./BrowserNotification');
const KaroNotification = require('./KaroNotification');

module.exports = Backbone.Model.extend(/** @lends KaroNotifier.prototype*/{
    defaults: {},
    /**
     * @constructor KaroNotifier
     * @class KaroNotifier
     * KaroNotifier manages all notifications to be shown on the screen
     * Provides custom methods as shortcuts for common notifications
     *
     */
    initialize(options) {
        _.bindAll(this, 'add', 'addGameMoveNotification', 'addUserDranNotification');
        const me = this;
        this.notifications = new Backbone.Collection();

        this.eventEmitter = options.eventEmitter;
        this.user = options.user;
        this.settings = options.settings;

        this.eventEmitter.on('CHAT:MESSAGE', (data) => {
            // console.warn(data.chatmsg);
            new BrowserNotification({
                title: data.user + ' spricht',
                body: data.text,
                level: 'info',
                group: 'global',
                tag: 'chat',
                icon: '/favicon.ico',
                timeout: 2000,
                notifyClick() {
                    alert('Geklickt');
                },
            });
        });

        this.eventEmitter.on('GAME:MOVE', (data) => {
            // skip unrelated
            if (!data.related) {
                if (me.user.get('id') == 1) {
                    // console.warn(data.movedLogin, "zog bei", data.gid, data.name);
                    // me.addGameMoveNotification(data);
                }
                return false;
            }

            if (me.user.get('id') == data.nextId) {
                me.addUserDranNotification(data);
            } else {
                me.addGameMoveNotification(data);
            }
        });
    },
    add(n) {
        this.notifications.add(n);

        const t = n.get('timeout');
        if (t !== 0) {
            const me = this;
            setTimeout(() => {
                me.remove(n);
            }, t);
        }
    },
    remove(n) {
        this.notifications.remove(n);
    },
    addGameMoveNotification(data) {
        if (data.name.length > 30) data.name = data.name.substring(0, 27) + '...';
        const text = 'Bei <a href="/game.html?GID=<%= gid %>"><%- name %></a> hat <%= movedLogin %> gerade gezogen. Jetzt ist <%= nextLogin %> dran'; // eslint-disable-line max-len
        const t = _.template(text);
        const n = new KaroNotification({
            text: t(data),
            level: 'info',
            group: 'global',
            imgUrl: '/images/preview/' + data.gid + '.png',
        });
        this.add(n);
    },
    addUserDranNotification(data) {
        const text = 'Du bist dran! Bei <a href="/game.html?GID=<%= gid %>"><%- name %></a> hat <%= movedLogin %> gerade gezogen.'; // eslint-disable-line max-len
        const t = _.template(text);
        const n = new KaroNotification({
            text: t(data),
            level: 'ok',
            group: 'dran',
            imgUrl: '/pre/' + data.gid + '.png',
        });
        this.add(n);
    },
});
