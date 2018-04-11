const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/map/mapInfo.html'),

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },
});
