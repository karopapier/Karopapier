// container for rendered map and players
const Marionette = require('backbone.marionette');
module.exports = Marionette.ItemView.extend({
    tagName: 'span',
    template: window['JST']['game/dranQueueItem'],
    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },
});

