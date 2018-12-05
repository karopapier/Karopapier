const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    tagName: 'a',
    template: require('../templates/preview.html'),
    onRender() {
        this.el.href = '/game.html?GID=' + this.model.get('id');
    },
});
