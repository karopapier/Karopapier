const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/newgame/lobbyUserFilter.html'),

    initialize() {
        this.listenTo(this.model, 'change:desperate', this.updateDesperate);
    },

    events: {
        'change @ui.desperate': 'checkDesperate',
        'input @ui.login': 'checkLogin'
    },

    ui: {
        desperate: '.filter-desperate',
        login: '.filter-login'
    },

    checkDesperate() {
        this.model.set('desperate', this.getUI('desperate').prop('checked'));
    },

    checkLogin(e) {
        this.model.set('login', this.getUI('login').val());
    },

    updateDesperate() {
        this.getUI('desperate').prop('checked', this.model.get('desperate'));
    }

});
