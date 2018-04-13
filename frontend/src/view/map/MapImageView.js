const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: () => {
    },
    tagName: 'img',
    className: 'map-image',

    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },

    onRender() {
        this.el.src = '//karopapier.de/images/maps/' + this.model.get('id') + '_1_0_1.png';
    },
});
