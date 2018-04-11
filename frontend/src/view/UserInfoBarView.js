'use strict';
const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../templates/userInfoBar.html'),

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },
});
