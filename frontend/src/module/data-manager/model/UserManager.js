const Radio = require('backbone.radio');
const Marionette = require('backbone.marionette');

// channels
const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');

module.exports = Marionette.Object.extend({

    initialize() {
        this.users = dataChannel.request('users');

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
