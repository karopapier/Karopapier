const _ = require('underscore');
const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    tagName: 'li',
    template: _.template('<%= login %>'),
    triggers: {
        click: 'select'
    }
});

