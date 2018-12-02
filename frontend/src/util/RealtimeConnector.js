const TURTED = require('turted-client');
const Backbone = require('backbone');
const Radio = require('backbone.radio');
const io = require('socket.io-client');
const OnlineStatus = require('../provider/OnlineStatus');

const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');

module.exports = Backbone.Model.extend({

    initialize() {
        this.user = dataChannel.request('user:logged:in');

        this.connected = false;
        this.hooked = false;
        this.identified = false;

        this.listenTo(this.user, 'change:u', () => {
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

        const host = this.config.realtimeHost;

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
                } else {
                    // quick fire "no websocket" info
                    const request = new XMLHttpRequest();
                    request.open('GET', '/transportlog.php?trans=' + transport, true);
                    request.send();
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
        const username = this.user.get('u');
        if (username) {
            // console.log('IDENT', username.toLocaleLowerCase(), this.config.token);
            this.turted.ident({
                username: username.toLowerCase(),
                token: this.config.token,
            });
            // } else {
            // console.log('Skip ident, no username');
        }
    },

    hook() {
        this.turted.on('PV', (data) => {
            appChannel.trigger('profile:visit', data.contact);
        });

        this.turted.on('MESSAGE:NEW', (data) => {
            appChannel.trigger('message:new', data);
        });

        this.turted.on('mail:new', (data) => {
            appChannel.trigger('mail:new', data);
        });

        // new read ts from mysql
        this.turted.on('mailcontact:ts', (data) => {
            appChannel.trigger('mailcontact:ts', data);
        });

        // deprecated from MONGO
        this.turted.on('CONTACT:READ', (data) => {
            appChannel.trigger('contact:read', data);
        });
        this.turted.on('CONTACT:DEL', (data) => {
            appChannel.trigger('contact:del', data);
        });
        this.turted.on('CONTACT:REMOVE', (data) => {
            appChannel.trigger('contact:remove', data);
        });

        // mysql
        this.turted.on('mailcontact:del', (data) => {
            appChannel.trigger('mailcontact:del', data);
        });
        this.turted.on('mailcontact:remove', (data) => {
            appChannel.trigger('mailcontact:remove', data);
        });

        this.turted.on('STATUS:ONLINE', (data) => {
            OnlineStatus.setOnline(data.c, true);
        });

        this.turted.on('STATUS:OFFLINE', (data) => {
            OnlineStatus.setOnline(data.c, false);
        });

        // PASSION
        this.turted.on('PICS:GRANTED', (data) => {
            appChannel.trigger('pics:granted', data);
        });

        this.turted.on('PICS:REVOKED', (data) => {
            appChannel.trigger('pics:revoked', data);
        });

        // mysql
        this.turted.on('pics:granted', (data) => {
            appChannel.trigger('pics:granted', data);
        });

        this.turted.on('pics:revoked', (data) => {
            appChannel.trigger('pics:revoked', data);
        });

        this.turted.on('task:update', (data) => {
            appChannel.trigger('task:update', data);
        });

        this.turted.on('systemMessage:new', (data) => {
            appChannel.trigger('systemMessage:new', data);
        });

        // User info hat sich im Backend geaendert (Club update, ...) und zwingt uns zum Refresh
        this.turted.on('user:force:update', () => {
            appChannel.trigger('user:force:update');
        });

        this.hooked = true;
    },

    unhook() {
        // @todo kann turted noch nicht
        // this.hooked = false;
    },
});
