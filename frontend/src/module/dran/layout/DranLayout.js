const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../templates/dranLayout.html'),
    regions: {
        list: {
            el: '.game-list',
            replaceElement: true,
        },
    },
});
