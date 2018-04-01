const Marionette = require('backbone.marionette');
const TextHelper = require('../../util/TextHelper');
module.exports = Marionette.View.extend({
    tagName: 'tr',
    template: require('../../../templates/game/drangame.html'),
    templateContext: {
        truncate: TextHelper.truncate
    }
});

