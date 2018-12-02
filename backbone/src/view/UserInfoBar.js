const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    id: 'userInfoBar',
    tagName: 'div',
    template: require('../../templates/main/userInfoBar.html'),
    events: {
        'click .login': 'login',
    },
    login(e) {
        e.preventDefault();
        console.log('Login now');
        return false;
    },
    initialize(options) {
        this.listenTo(this.model, 'change', this.render);
    },
});
