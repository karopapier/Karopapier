const Radio = require('backbone.radio');
const Marionette = require('backbone.marionette');

// channels
const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');

module.exports = Marionette.Object.extend({

    initialize() {
        this.dranGames = dataChannel.request('drangames');
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
    },
});
