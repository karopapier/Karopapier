const _ = require('underscore');
const Backbone = require('backbone');
const UserView = require('./UserView');

module.exports = Backbone.View.extend({
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
        _.bindAll(this, 'render');
        this.userView = new UserView({
            model: this.model,
            withGames: true,
            withAnniversary: true,
            withDesperation: false,
            withGamesLink: true,
        });
        this.listenTo(this.model, 'change', this.render);
    },
    render() {
        let uid = this.model.get('id');
        if (uid > 0) {
            this.$el.html(this.userView.$el);
            this.$el.append(' ');
            let uc = this.model.get('uc');
            if (uc > 0) {
                this.$el.append('<a href="//karopapier.de/zettel"><span class="unread">' + uc + '</span></a>');
            }
            this.$el.append(this.template());
            return this;
        }

        let html = 'Moment, kenn ich Dich?';
        if (uid === 0) {
            html = '<a class="login" href="/login">Nicht angemeldet</a>';
        }
        this.$el.html(html);
        return this;
    },
});
