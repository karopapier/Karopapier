const Marionette = require('backbone.marionette');
module.exports = Marionette.ItemView.extend({
    template: window.JST['editor/imagetranslatorinfo'],
    initialize() {
        this.listenTo(this.model, 'change', this.render);
    },
});
