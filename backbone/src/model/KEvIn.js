const _ = require('underscore');
const Backbone = require('backbone');
const TURTED = require('turted-client');

module.exports = Backbone.Model.extend(/** @lends KEvIn.prototype*/{
    defaults: {},
    /**
     * @constructor KEvIn
     * @class KEvIn
     * Karo EVent INterfcae - handling and forwarding real time notifications, forwarding them to the KaroApp
     *
     */
    initialize(options) {
        options = options || {};
        // console.log("Run init on KEvIn");
        _.bindAll(this, 'ident', 'hook', 'start', 'stop');
        if (!options.user) {
            throw Error('KEvIn needs a user');
        }

        let host = '//ws01.karopapier.de';
        if (options.host) {
            host = options.host;
        }

        if (!options.vent) {
            throw Error('KEvIn needs a vent object to trigger events on');
        }
        this.user = options.user;
        this.vent = options.vent;
        this.listenTo(this.user, 'change:id', this.ident);
        this.turted = new TURTED(host);
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
        // simple trigger for a new move - consider skipping it for the more eloquent GAME:MOVE with my id
        this.turted.on('yourTurn', (data) => {
            // console.log("SKIPPED - yourTurn not forwared")
            // Karopapier.vent.trigger("USER:DRAN", data);
        });

        // simple trigger for when you moved
        this.turted.on('youMoved', (data) => {
            // console.info("USER:MOVED aus youMoved");
            // console.log("SKIPPED - youMoved not forwared")
            // Karopapier.vent.trigger("USER:MOVED", data);
        });

        // detailed trigger if a game related to you saw a move
        const me = this;
        this.turted.on('otherMoved', (data) => {
            data.related = true;
            // console.info("GAME:MOVE aus otherMoved");
            me.vent.trigger('GAME:MOVE', data);

            // bei wieder dran, erst runter dann hoch zaehlen
            // daher erst "moved" triggern, dann dran
            if (me.user.get('id') == data.movedId) {
                // console.info("USER:MOVED aus otherMoved");
                me.vent.trigger('USER:MOVED', data);
            }
            if (me.user.get('id') == data.nextId) {
                // console.info("USER:DRAN aus otherMoved");
                me.vent.trigger('USER:DRAN', data);
            }
        });

        //
        this.turted.on('anyOtherMoved', (data) => {
            data.related = false;
            // console.info("GAME:MOVE aus anyOtherMoved");
            me.vent.trigger('GAME:MOVE', data);
        });
        this.turted.on('CHAT:MESSAGE', (data) => {
            // console.info("CHAT:MESSAGE");
            me.vent.trigger('CHAT:MESSAGE', data.chatmsg);
        });

        this.turted.on('msg', (data) => {
            me.vent.trigger('message:new', data);
        });
    },
    start() {
        this.turted.join('karochat');
        this.turted.join('livelog');
    },
    stop() {
        // this.turted.leave("karochat");
        // this.turted.leave("livelog");
    },
});
