var _ = require('underscore');
var Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    tagName: "li",
    template: _.template('<%= login %>'),
    triggers: {
        "click": "select"
    }
});

