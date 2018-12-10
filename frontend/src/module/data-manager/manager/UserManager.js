const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');

const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');

const UserCollection = require('../collection/UserCollection');

module.exports = Marionette.Object.extend({

    initialize() {
        this.users = new UserCollection();
        this.users.url = '/api/users';

        /*
        const cached = localStorage.getItem('users');

        if (cached) {
            this.users.reset(JSON.parse(localStorage.getItem('users')));
            this.users.trigger('loaded');
        }

        this.users.once('sync', () => {
            localStorage.setItem('users', JSON.stringify(this.users.toJSON()));
        });

        */

        this.users.fetch();

        this.authUser = dataChannel.request('user:logged:in');

        dataChannel.reply('users', () => {
            return this.users;
        });

        // handle realtime updates of dranGames
        appChannel.on('game:move', (data) => {
            // skip events that are "related to me", as they would result in double counts
            if (data.related) {
                return;
            }
            // console.log('Someone moved', data);
            this.moved(data.movedId);
            this.next(data.nextId);
        });

        appChannel.on('user:moved', () => {
            this.authUser.decDran();
        });
        appChannel.on('user:dran', () => {
            this.authUser.incDran();
        });
    },

    moved(id) {
        // console.log('es bewegte sich', id);
        const user = this.users.get(id);
        // console.log('Das ist ', user.get('login'));
        if (user) {
            user.decDran();
        }
    },

    next(id) {
        // console.log('Dafür ist nun dran', id);
        const user = this.users.get(id);
        // console.log('Das ist ', user.get('login'));
        if (user) {
            user.incDran();
        }
    },
});
