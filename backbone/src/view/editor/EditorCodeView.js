const Backbone = require('backbone');
const MapCodeView = require('../map/MapCodeView');

module.exports = Backbone.View.extend({
    initialize(options) {
        options = options || {};
        if (!options.model) {
            console.error('No map for EditorCodeView');
            return false;
        }
    },
    events: {
        'blur .mapCodeView': 'sanity',
    },
    sanity() {
        this.model.sanitize();
    },
    render() {
        let mcv = new MapCodeView({
            className: 'mapCodeView',
            model: this.model,
            readonly: false,
        });
        this.$el.append(mcv.$el);
    },
});
