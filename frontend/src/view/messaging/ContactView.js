var Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    className: "clickable",
    template: require('../../../templates/messaging/contact.html'),

    triggers: {
        "click": "contact:select"
    },

    initialize: function() {
        var me = this;
        this.listenTo(this.model, "change:selected", this.render);
        this.listenTo(this.model, "change:uc", this.render);
    },

    onRender: function() {
        if (this.model.get("selected")) {
            this.$el.addClass("selected");
        } else {
            this.$el.removeClass("selected");
        }
    }
});

