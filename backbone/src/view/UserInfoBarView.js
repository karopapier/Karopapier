const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'userInfoBar',
    template: require('../../templates/user/userinfobar.html'),

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },
});
