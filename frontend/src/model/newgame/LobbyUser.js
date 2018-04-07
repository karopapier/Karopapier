const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    defaults: {
        selected: false,
        exceeded: false
    }
});
