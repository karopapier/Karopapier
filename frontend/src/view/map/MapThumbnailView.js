const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    className: 'map-thumbnail',
    template: require('../../../templates/map/mapThumbnail.html'),

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },
});
