const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/newgame/lobbyUser.html'),

    events: {
        'click': 'toggleSelect'
    },

    initialize() {
        this.listenTo(this.model, 'change:selected', this.render);
    },

    toggleSelect() {
        this.model.set('selected', !(this.model.get('selected')));
    },

    onRender() {
        const classes = ['lobbyUser', 'clickable'];
        if (this.model.get('selected')) {
            classes.push('mod-selected');
        }

        if (this.model.get('exceeded')) {
            classes.push('mod-exceeded');
        }

        if (!(this.model.get('acceptsDayGames') || this.model.get('acceptsNightGames'))) {
            classes.push('mod-warning');
        }

        if (this.model.get('desperate')) {
            classes.push('mod-desperate');
        }

        this.el.className = classes.join(' ');
    }
});
