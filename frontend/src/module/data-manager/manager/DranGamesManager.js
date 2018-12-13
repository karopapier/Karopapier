const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');

const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');

const GameCollection = require('../collection/GameCollection');

module.exports = Marionette.Object.extend({
    initialize() {
        this.dranGames = new GameCollection();
        this.authUser = dataChannel.request('user:logged:in');
        this.authUser.getLoadedPromise().then(() => {
            if (this.authUser.isLoggedIn()) {
                this.dranGames.url = '/api/user/' + this.authUser.get('id') + '/dran';
                this.dranGames.fetch();
            }
        });

        this.dranGames.comparator = (game1, game2) => {
            // const gamesort = this.authUser.get('gamesort');
            const gamesort = 'blocktime';
            if (gamesort === 'blocktime') {
                if (game1.get('blocked') > game2.get('blocked')) {
                    return -1;
                }
                return 1;
            }
        };

        dataChannel.reply('dranGames', () => {
            return this.dranGames;
        });

        // handle realtime updates of dranGames
        appChannel.on('user:moved', (data) => {
            const gid = data.gid;
            console.log('Have to remove from dran', data);
            this.dranGames.remove(gid);
        });

        appChannel.on('user:dran', (data) => {
            console.log('Have to add to dran', data);
            const g = {
                id: data.gid,
                name: data.name,
                dranName: data.nextLogin,
                blocked: new Date().getHours() + ':' + new Date().getMinutes(),
            };
            this.dranGames.add(g);
        });

        setInterval(() => {
            this.dranGames.fetch();
        }, 120000);
    },

    getCollection() {
        return this.dranGames;
    },
});
