const Backbone = require('backbone');
const MapCodeView = require('../map/MapCodeView');

module.exports = Backbone.View.extend({
    initialize: function(options) {
        options = options || {};
        if (!options.model) {
            console.error('No map for EditorCodeView');
            return false;
        }
    },
    events: {
        'blur .mapCodeView': 'sanity',
    },
    sanity: function() {
        this.model.sanitize();
    },
    render: function() {
        let mcv = new MapCodeView({
            className: 'mapCodeView',
            model: this.model,
            readonly: false,
        });
        this.$el.append(mcv.$el);
    },
});
