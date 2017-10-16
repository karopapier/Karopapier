var Backbone = require('backbone');
var Radio = require('backbone.radio');
var Marionette = require('backbone.marionette');

// Layout
var MessagingLayout = require('../layout/MessagingLayout');

//Model
var User = require('../model/User');

// Collections
var MessageCollection = require('../collection/MessageCollection');
var UserCollection = require('../collection/UserCollection');
var ContactCollection = require('../collection/ContactCollection');

// Views
var SendView = require('../view/messaging/SendView');
var ContactDetailsView = require("../view/messaging/ContactDetailsView");
var MessagesView = require('../view/messaging/MessagesView');
var ContactsView = require('../view/messaging/ContactsView');
var AddContactView = require('../view/messaging/AddContactView');

window.USERS = new UserCollection();

var MessagingRouter = Backbone.Router.extend({
    initialize: function(options) {
        this.app = options.app;
    },
    routes: {
        "zettel/:contact": "select",
        "zettel": "index"
    },
    index: function() {
        this.app.unselect();
    },
    select: function(contactName) {
        this.app.selectName(contactName);
    }
});

module.exports = Marionette.Application.extend({
    initialize: function() {
        Backbone.emulateHTTP = true;
        var me = this;
        this.authUser = new User();
        this.authUser.url = '/api/users/check';
        this.authUser.fetch();

        this.contacts = new ContactCollection();
        this.messages = new MessageCollection();
        this.userMessages = new MessageCollection();
        this.listenTo(this.messages, 'add', function(m) {
            //console.log("Add", m);
            //me.unreadRecalc();
            var selectedContact = me.getSelectedContact();
            if (selectedContact) {
                if (m.get("contact_id") === selectedContact.get("id")) {
                    this.userMessages.add(m);
                }
            }
        });

        this.dataProvider = Radio.channel('data');
        this.dataProvider.reply("user:logged:in", function() {
            return me.authUser
        });

        this.messagingLayout = new MessagingLayout({
            el: '#messaging',
            triggers: {
                "click .backnav": "unselect"
            }
        });
    },

    start: function() {
        console.info("Start App");
        var me = this;
        this.unreadRecalc();
        this.messagingLayout.render();

        this.contactsView = new ContactsView({
            collection: this.contacts
        });

        this.messagingLayout.getRegion("contacts").show(this.contactsView);

        this.listenTo(this.messagingLayout, "unselect", function() {
            me.unselect();
        });

        this.listenTo(this.contactsView, "childview:contact:select", function(e) {
            var contact = e.model;
            me.select(contact);
        });
        this.addContactView = new AddContactView({
            viewComparator: 'login',
            collection: USERS
        });
        this.messagingLayout.getRegion("addcontact").show(this.addContactView);
        this.addContactView.on("select", function(contactName) {
            me.selectName(contactName);
        });

        this.router = new MessagingRouter({
            app: this
        });
        Backbone.history.start({pushState: true});
    },

    selectName: function(contactName) {
        console.info("Select", contactName);
        var c = this.contacts.findWhere({
            "login": contactName
        });
        if (!c) {
            c = USERS.findWhere({
                "login": contactName
            });

            if (!c) {
                console.warn("Not found");
                return false;
            }
            this.contacts.add(c);
        }

        this.select(c);
    },

    select: function(contact) {
        var me = this;
        this.contacts.each(function(c) {
            c.set("selected", contact.get("id") === c.get("id"))
        });
        var messages = this.messages.where({
            contact_id: contact.get("id")
        });
        var prevDate = "";
        var uc = 0;
        messages.forEach(function(m) {
            var d = new Date(m.get("ts") * 1000);
            var dat = "" + d.getDate() + d.getMonth();
            if (dat !== prevDate) {
                m.set("dateSeparator", true);
                prevDate = dat;
            }
            if (!m.get("r") && (m.get("rxtx") === "rx")) uc++;
        });
        this.userMessages.reset(messages);
        this.messagingLayout.getRegion("messages").show(new MessagesView({
            collection: this.userMessages
        }));
        this.sendView = new SendView({
            model: contact
        });
        this.messagingLayout.getRegion("send").show(this.sendView);
        this.messagingLayout.$el.addClass("js-selected");
        this.messagingLayout.$el.removeClass("js-unselected");
        this.listenTo(this.sendView, "send", function(data) {
            me.messages.create(data, {
                wait: true,
                success: function() {
                    me.sendView.reset();
                },
                error: function() {
                    me.sendView.enable();
                    //Fehler beim Versand
                }
            });
        });

        this.messagingLayout.getRegion("contactInfo").show(new ContactDetailsView({
            model: contact
        }));

        if (uc > 0) {
            contact.setAllRead();
        }

        Backbone.history.navigate("zettel/" + contact.get("login"));
    },

    unselect: function() {
        this.contacts.each(function(c) {
            c.set("selected", false);
        });
        this.userMessages.reset([]);
        this.messagingLayout.$el.addClass("js-unselected");
        this.messagingLayout.$el.removeClass("js-selected");
        this.messagingLayout.getRegion("send").empty();
        this.messagingLayout.getRegion("contactInfo").empty();
        Backbone.history.navigate("zettel");
    },

    unreadRecalc: function() {
        console.info("Unread recalc");
        var me = this;
        this.contacts.each(function(c) {
            var uc = me.messages.where({
                r: 0,
                "contact_id": c.get("id"),
                rxtx: "rx"
            });
            c.set("uc", uc.length);
        })
    },

    getSelectedContact: function() {
        return this.contacts.findWhere({selected: true});
    }
});
