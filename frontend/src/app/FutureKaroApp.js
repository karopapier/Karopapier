const Radio = require('backbone.radio');
const Marionette = require('backbone.marionette');
module.exports = Marionette.Application.extend({

    initialize: function() {
        console.info('App init');
    },

    start: function() {
        console.info('App start');
    },

    register: function() {
        this.navigator = Radio.channel('navigator');
    }
});
