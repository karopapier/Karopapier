const Marionette = require('backbone.marionette');
const TextHelper = require('../../../util/TextHelper');
module.exports = Marionette.View.extend({
    tagName: 'tr',
    template: require('../templates/drangame.html'),
    templateContext: {
        truncate: TextHelper.truncate,
    },
});

