const $ = require('jquery');
const Marionette = require('backbone.marionette');
const dateHelper = require('../../../util/DateHelper');

module.exports = Marionette.View.extend({
    className: 'chat-karolenderblatt-container clickable',
    template: require('../templates/chat-karolenderblatt.html'),
    initialize() {
        console.log('Karolenderblatt');
        this.blaetter = [];
    },
    events: {
        'click .chat-karolenderblatt': 'show',
        'click .chat-karolenderblatt-modal': 'close',
    },

    show() {
        if (this.blaetter.length <= 0) {
            this.blaetter.push({'posted': '', 'line': 'Suche...'});
            $.getJSON('/api/karolenderblatt/' + dateHelper.getCurrentYmd(), (data) => {
                this.blaetter = data;
                console.log(this.blaetter);
                this.render();
            });
        }
        this.render();
    },

    close() {
        this.blaetter = [];
        this.render();
    },

    templateContext() {
        return {
            dateHelper,
            blaetter: this.blaetter,
        };
    },
});
