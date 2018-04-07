const Backbone = require('backbone');
module.exports = Backbone.Model.extend({
    defaults: {
        login: '',
        desperate: false
    }
});
