const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

// Models
const PlayerFilter = require('../model/newgame/PlayerFilter');

// Views
const PlayerFilterView = require('../view/newgame/PlayerFilterView');
const NewGameLayout = require('../layout/NewGameLayout');

module.exports = Marionette.Application.extend({

    initialize(config) {
        console.log('Init NewGame App');

        this.layout = new NewGameLayout({});

        this.loadInitialAndStart();
    },

    loadInitialAndStart() {
        this.players = dataChannel.request('users');
        this.start();
    },

    start() {
        console.info('Start NewGame App');
        this.playerFilter = new PlayerFilter();
        this.layout.getRegion('playerfilter').show(new PlayerFilterView({
            model: this.playerFilter
        }));

        /*
        this.layout.getRegion('playerlist').show(new PlayerlistView({
            collection: this.players,
            filterModel: this.playerFilter
        }));
        */
    }
});
