const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    options: {
        withAnniversary: true,
        withGames: false,
        withDesperation: false,
        withGamesLink: false,
        withInfoLink: false,
    },
    tagName: 'span',
    template: require('../../templates/user/userView.html'),
    initialize: function(options) {
        if (!this.model) {
            console.error('No model!');
            return false;
        }
        this.options = _.defaults(options || {}, this.options);
        _.bindAll(this, 'dranChange', 'render', 'onChange');

        // console.log("Init UserView", this.model.get("login"));
        this.listenTo(this.model, 'change', this.onChange);
        // this.listenTo(this.model, "change", this.render);
        // this.listenTo(this.model, "change:dran", this.dranChange);
        // this.listenTo(this.model, "remove", this.remove);

        this.render();
    },
    onChange: function(e) {
        // if dran is the only changed property
        if (e.changed.dran && _.size(e.changed) == 1) {
            this.dranChange(e);
            return true;
        }
        this.render();
    },
    dranChange: function(user) {
        // @TODO Flacker wieder einbauen
        console.log('Dran change Animation kaputt gemacht');
    },
});
