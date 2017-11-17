'use strict';
// const Backbone = require('backbone');
const Radio = require('backbone.radio');
const Marionette = require('backbone.marionette');
const $ = require('jquery');
const GameRouter = require('../router/GameRouter');
const GameLayout = require('../layout/GameLayout');

module.exports = Marionette.Application.extend({

    initialize(config) {
        console.log('Init Game App');

        this.navigator = Radio.channel('navigator');
        this.navigator.on('spiele', (gid) => {
            this.show(gid);
        });

        this.layout = new GameLayout({});

        this.loadInitialAndStart();
    },

    loadInitialAndStart() {
        let me = this;
        $.when(
        ).done(function() {
            me.start();
        });
    },

    start() {
        console.info('Start Game App');
        this.router = new GameRouter({
            app: this
        });
    },

    show(gid) {
        console.log('Game has to show', gid);
    }
});
