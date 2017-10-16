const Backbone = require('backbone');
module.exports = Backbone.Model.extend({
    defaults: {
        uc: 0,
        selected: false
    },

    setAllRead: function() {
        this.save({r: true}, {
            patch: true,
            wait: true
        });
    }
});

