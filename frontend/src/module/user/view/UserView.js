const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    options: {
        withAnniversary: true,
        withGames: false,
        withDesperation: false,
        withGamesLink: false,
        withInfoLink: false,
    },
    tagName: 'span',
    template: require('../templates/user-view.html'),
    templateContext() {
        return {
            options: this.options,
        };
    },

    ui: {
        dran: '.js-dran',
    },

    initialize(options) {
        // this.options = _.defaults(options || {}, this.options);

        // console.log("Init UserView", this.model.get("login"));
        this.listenTo(this.model, 'change', this.onChange);
        // this.listenTo(this.model, "change", this.render);
        // this.listenTo(this.model, "change:dran", this.dranChange);
        // this.listenTo(this.model, "remove", this.remove);
    },

    onChange(e) {
        // if dran is the only changed property
        if (Object.keys(e.changed).length === 1) {
            if (e.changed.dran) {
                this.dranChange(this.model.previous('dran'), e.changed.dran);
                return true;
            }
        }
        this.render();
    },

    dranChange(from, to) {
        this.$el.removeClass('blink-red');
        this.$el.removeClass('blink-green');

        if (from < to) {
            this.$el.addClass('blink-red');
        } else {
            this.$el.addClass('blink-green');
        }
        console.log(this.getUI('dran'));
        this.getUI('dran').text(to);
    },
});
