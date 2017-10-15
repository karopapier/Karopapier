var Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/messaging/sendview.html'),
    tagName: "form",
    events: {
        "submit": "send"
    },

    send: function(e) {
        var text = this.$('.send-text').val().trim();
        if (text.length === 0) {
            e.preventDefault();
            return false;
        }
        var userId = this.model.get("id");
        console.info("Nachricht", text, "an", userId);

        this.trigger("send", {
            userId: userId,
            text: text
        });
        this.$('input[type=submit]').prop("disabled", true);
        e.preventDefault();
    },

    reset: function() {
        this.$('.send-text').val("");
        this.enable();
    },

    enable: function() {
        this.$('input[type=submit]').prop("disabled", false);
    }
});

