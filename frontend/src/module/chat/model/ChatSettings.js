const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    defaults: {
        history: false,
        funny: true,
        limit: 20,
        lastLineId: 0,
        showBotrix: false,
        oldLink: false,
    },
});
