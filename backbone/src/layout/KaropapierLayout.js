const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    regions: {
        header: '#header',
        navi: '#navi',
        content: '#content',
        footer: '#footer',
    },
});
