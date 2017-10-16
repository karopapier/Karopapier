const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    tagName: 'option',
    template: _.template('<%= login %>'),
    attributes: function() {
        return {
            value: this.model.get('id')
        };
    }
});

