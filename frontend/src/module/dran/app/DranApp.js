'use strict';
const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const DranLayout = require('../layout/DranLayout');
const DranGamesView = require('../view/DranGamesView');

module.exports = Marionette.Application.extend({

    initialize() {
        console.log('Init Dran App');

        this.navigator = Radio.channel('navigator');

        this.layout = new DranLayout({});
        this.dranGames = dataChannel.request('dranGames');

        this.start();
    },

    start() {
        console.info('Start Dran App');

        this.layout.getRegion('list').show(new DranGamesView({
            collection: this.dranGames,
        }));
    },
});
