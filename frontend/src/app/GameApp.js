'use strict';
const Backbone = require('backbone');
const Radio = require('backbone.radio');
const Marionette = require('backbone.marionette');
const $ = require('jquery');

module.exports = Marionette.Application.extend({
    initialize(config) {
        console.log("Init Game App");
        this.loadInitialAndStart();

        this.layout = new Marionette.View({
            template() {
                return "Lala";
            }
        });
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
    }
});
