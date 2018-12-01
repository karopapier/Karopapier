const _ = require('underscore');
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
    template: require('../../templates/user/userView.html'),
    initialize(options) {
        this.options = _.defaults(options || {}, this.options);

        // console.log("Init UserView", this.model.get("login"));
        // this.listenTo(this.model, 'change', this.onChange);
        // this.listenTo(this.model, "change", this.render);
        // this.listenTo(this.model, "change:dran", this.dranChange);
        // this.listenTo(this.model, "remove", this.remove);

        setInterval(() => {
            this.model.set('dran', Math.floor(Math.random() * 100));
        }, 3500);
        this.render();
    },
    onChange(e) {
        // if dran is the only changed property
        if (e.changed.dran && _.size(e.changed) == 1) {
            this.dranChange(e);
            return true;
        }
        this.render();
    },
    dranChange(user) {
        // @TODO Flacker wieder einbauen
        console.log('Dran change Animation kaputt gemacht');
    },
});
