const Backbone = require('backbone');
const Radio = require('backbone.radio');
const TURTED = require('turted-client');
module.exports = Backbone.Model.extend(/** @lends KEvIn.prototype */{
    defaults: {},
    /**
     * @constructor KEvIn
     * @class KEvIn
     * Karo EVent INterfcae - handling and forwarding real time notifications, forwarding them to the KaroApp
     *
     */
    initialize() {
        // console.log("Run init on KEvIn");
        const dataChannel = Radio.channel('data');
        this.user = dataChannel.request('user:logged:in');
        const config = dataChannel.request('config');

        let turtedHost = '';
        if (config.turtedHost) {
            turtedHost = config.turtedHost;
        }
        if (!turtedHost) {
            console.error('NO HOST CONFIG');
        }

        this.appChannel = Radio.channel('app');
        this.messagingChannel = Radio.channel('messaging');

        this.listenTo(this.user, 'change:id', this.ident);
        this.turted = new TURTED(turtedHost);
        this.ident();
        this.hook();
    },

    ident() {
        const user = this.user;

        if (user.get('id') === 0) {
            this.stop();
        } else {
            this.turted.ident({'username': this.user.get('login')});
            this.start();
        }
    },

    hook() {
        // detailed trigger if a game related to you saw a move
        const me = this;

        this.turted.on('otherMoved', (data) => {
            data.related = true;
            this.appChannel.trigger('game:move', data);
            if (this.user.get('id') === data.movedId) {
                this.appChannel.trigger('user:moved', data);
            }
            if (this.user.get('id') === data.nextId) {
                this.appChannel.trigger('user:dran', data);
            }
        });

        this.turted.on('anyOtherMoved', function(data) {
            data.related = false;
            me.appChannel.trigger('game:move', data);
        });

        this.turted.on('chat:message', function(data) {
            me.appChannel.trigger('chat:message', data);
        });

        this.turted.on('msg', (data) => {
            this.messagingChannel.trigger('message:new', data);
        });
    },

    start() {
        this.turted.join('karochat');
        this.turted.join('livelog');
    },

    stop() {
        // this.turted.leave("karochat");
        // this.turted.leave("livelog");
    }
});
