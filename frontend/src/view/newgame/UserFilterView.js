const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/newgame/userfilter.html'),

    initialize() {
        this.listenTo(this.model, 'change:desperate', this.updateDesperate);
    },

    events: {
        '@ui.desperate change': 'checkDesperate'
    },

    ui: {
        desperate: 'input[type=checkbox]'
    },

    checkDesperate() {
        this.model.set('desperate', this.getUI('desperate').prop('checked'));
    },

    updateDesperate() {
        this.getUI('desperate').prop('checked', this.model.get('desperate'));
    }

});
