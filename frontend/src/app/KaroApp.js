var Backbone = require('backbone');
var Radio = require('backbone.radio');
var Marionette = require('backbone.marionette');
module.exports = Marionette.Application.extend({

    initialize: function() {
        console.info("App init");

    },

    start: function() {
        console.info("App start");

    },

    register: function() {

        this.navigator = Radio.channel("navigator");

    }

});