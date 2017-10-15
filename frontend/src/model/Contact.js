var Backbone = require('backbone');
module.exports = Backbone.Model.extend({
    defaults: {
        uc: 0,
        selected: false
    },

    setAllRead: function() {
        //console.warn("PUT contact");
        this.save({r: true}, {
            patch: true,
            wait: true
            //success: function() {
            //console.log("JEzt alle 0");
            //}
        });
    }
});

