var Marionette = require('backbone.marionette');
var UserCollection = require('../../collection/UserCollection');
var UserlistView = require('./UserlistView');
module.exports = Marionette.View.extend({
    template: require('../../../templates/messaging/addContact.html'),

    events: {
        "focus input": "autocomplete",
        "input input": "autocomplete",
        "click button": "select"
    },

    regions: {
        "auto": ".js-auto"
    },

    initialize: function() {
        var me = this;
        this.collection = new UserCollection();
        this.listview = new UserlistView({
            collection: this.collection
        });
        this.listenTo(this.listview, "childview:select", function(e) {
            //console.log("click", e);
            me.insert(e.model);
            me.unautocomplete();
        });
        this.listview.$el.css({
            maxHeight: 60,
            overflow: "auto",
            "backgroundColor": "white",
            "border": "1px solid black"
        });
    },
    autocomplete: function(e) {
        var typed = this.$('input').val().toLowerCase();
        //console.log("show & update auto", typed);
        this.collection.reset(USERS.filter(function(m) {
            return !m.get("login").toLowerCase().indexOf(typed);
        }));
        this.listview = this.getRegion("auto").show(this.listview);
        this.getRegion("auto").$el.show();
    },

    unautocomplete: function() {
        this.getRegion("auto").$el.hide();
    },

    insert: function(m) {
        //console.log("INsert", m.get("login"));
        this.$('input').val(m.get("login"));
    },

    select: function() {
        this.trigger("select", this.$('input').val());
    }
});

