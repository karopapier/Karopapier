var Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    tagName: "span",
    template: require("../../../templates/messaging/contactDetails.html")
});
