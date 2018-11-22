const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    tagName: 'option',
    template: _.template('<%= login %>'),
    attributes() {
        return {
            value: this.model.get('id'),
        };
    },
});

