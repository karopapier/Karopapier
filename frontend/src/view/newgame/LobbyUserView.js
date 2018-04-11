const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const appChannel = Radio.channel('app');

module.exports = Marionette.View.extend({
    template: require('../../../templates/newgame/lobbyUser.html'),

    events: {
        'click': 'toggleSelect'
    },

    initialize() {
        this.listenTo(this.model, 'change:selected', this.render);
    },

    toggleSelect() {
        appChannel.trigger('lobbyuser:select:toggle', this.model.get('id'));
    },

    onRender() {
        const classes = ['user-slot', 'clickable'];
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
