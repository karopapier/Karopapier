const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    defaults: {
        user: 'KaroMAMMA',
        text: 'Das tut noch nich',
        time: '00:00',
    },
});
