var Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require("../../templates/messaging/messagingLayout.html"),
    regions: {
        send: ".send-view",
        messages: ".message-list",
        contactInfo: ".contact-info",
        contacts: ".contact-list",
        addcontact: ".contact-add"
    }
});

