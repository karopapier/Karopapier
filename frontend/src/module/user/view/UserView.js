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


        // for deferred dran change handling
        this.oldFrom = -1;
        this.dranChangeTimeout = -1;
    },

    onChange(e) {
        // if dran is the only changed property
        if (Object.keys(e.changed).length === 1) {
            if (e.changed.dran) {
                this.deferredDranChange(this.model.previous('dran'), e.changed.dran);
                return true;
            }
        }
        this.render();
    },

    /**
     * Check, if there is a previous deferred from value
     * If so, keep it, reset deferred blink timeout
     * @param from
     * @param to
     */
    dranChange(from, to) {
        // console.log('Defered change', from, to);
        // console.log('Deferred change from', this.oldFrom, 'to', to);
        if (this.oldFrom < to) {
            this.$el.addClass('blink-red');
        } else {
            this.$el.addClass('blink-green');
        }
        setTimeout(() => {
            this.$el.removeClass('blink-green');
            this.$el.removeClass('blink-red');
        }, 2500);
        this.oldFrom = -1;
        this.getUI('dran').text(to);
    },

    deferredDranChange(from, to) {
        if (this.oldFrom < 0) {
            this.oldFrom = from;
        }

        clearTimeout(this.dranChangeTimeout);

        this.dranChangeTimeout = setTimeout(this.dranChange.call(this, from, to), 50);
    },
});
