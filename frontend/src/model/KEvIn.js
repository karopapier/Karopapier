const Backbone = require('backbone');
const Radio = require('backbone.radio');
const TURTED = require('turted-client');
const io = require('socket.io-client');
const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');
const messagingChannel = Radio.channel('messaging');

module.exports = Backbone.Model.extend(/** @lends KEvIn.prototype */{
    /**
     * @constructor KEvIn
     * @class KEvIn
     * Karo EVent INterfcae - handling and forwarding real time notifications, forwarding them to the KaroApp
     *
     */
    initialize() {
        this.user = dataChannel.request('user:logged:in');

        this.connected = false;
        this.hooked = false;
        this.identified = false;

        this.listenTo(this.user, 'change:login', () => {
            // console.log('Im RT hat sich ein User geändert');
            this.update();
        });
    },

    update() {
        // connected?
        // hooked?
        // ident?

        if (this.user.isLoggedIn()) {
            this.connect();
            return;
        }

        // console.log('make sure to be disconnected');
        this.disconnect();
    },

    connect() {
        this.config = dataChannel.request('config');

        const host = this.config.turtedHost;

        // prepare IO to only use websocket, no polling, if previously connected with websocket
        let transports = ['polling', 'websocket'];
        try {
            if (sessionStorage.getItem('WS') === 'ok') {
                // console.log('WS IS OK');
                transports = ['websocket'];
            }
        } catch (e) {
            // console.log('No storage')
        }
        this.socket = io(host, {
            transports,
        });

        // Wenn connected, check nach 5 Sekunden, ob websocket oder polling
        // bei "connected" ist zu Beginn erst mal polling, daher auf upgrade warten
        this.socket.on('connect', () => {
            this.connected = true;

            setTimeout(() => {
                const transport = this.socket.io.engine.transport.name;
                // console.log('DANN SCHAU MA MA', transport);
                if (transport === 'websocket') {
                    // console.info('Established websocket');
                    try {
                        sessionStorage.setItem('WS', 'ok');
                    } catch (e) {
                        console.info('No storage', e);
                    }
                }
            }, 5000);
        });

        this.turted = new TURTED(host, this.socket);
        this.hook();
        this.ident();
    },

    disconnect() {
        if (this.connected) {
            if (this.socket) {
                this.socket.disconnect(() => {
                    // console.log('Bin jetz disconnected und kann den socket wegwerfen');
                });
            }
        }
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
        this.turted.on('otherMoved', (data) => {
            data.related = true;
            appChannel.trigger('game:move', data);
            if (this.user.get('id') === data.movedId) {
                appChannel.trigger('user:moved', data);
            }
            if (this.user.get('id') === data.nextId) {
                appChannel.trigger('user:dran', data);
            }
        });

        this.turted.on('anyOtherMoved', (data) => {
            data.related = false;
            appChannel.trigger('game:move', data);
        });

        this.turted.on('CHAT:MESSAGE', (data) => {
            appChannel.trigger('chat:message', data);
        });

        this.turted.on('msg', (data) => {
            messagingChannel.trigger('message:new', data);
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
