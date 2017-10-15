var $ = require('jquery');
var _ = require('underscore');
var Backbone = require('backbone');
var Marionette = require('backbone.marionette');

var MessagingLayout = require('../layout/MessagingLayout');
var ContactDetailsView = require("../view/messaging/ContactDetailsView");
var UserCollection = Backbone.Collection.extend({
    url: "/api/users"
});

var Message = Backbone.Model.extend({
    defaults: {
        dateSeparator: false
    }
});

var MessageCollection = Backbone.Collection.extend({
    model: Message,
    comparator: "ts",
    url: "/api/messages"
});

window.USERS = new UserCollection();

var Contact = Backbone.Model.extend({
    defaults: {
        uc: 0,
        selected: false
    },

    setAllRead: function() {
        console.warn("PUT contact");
        this.save({r: true}, {
            patch: true,
            wait: true,
            success: function() {
                console.log("JEzt alle 0");
            }
        });
    }
});

var ContactCollection = Backbone.Collection.extend({
    model: Contact,
    url: "/api/contacts"
});

var SendView = Marionette.View.extend({
    tagName: "form",
    events: {
        "submit": "send"
    },
    template: "#send-template",

    send: function(e) {
        var text = this.$('.send-text').val().trim();
        if (text.length == 0) {
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

var UserOptionView = Marionette.View.extend({
    tagName: "option",
    template: _.template("<%= login %>"),
    attributes: function() {
        return {
            value: this.model.get("id")
        }
    }
});

var UserDropdownView = Marionette.CollectionView.extend({
    tagName: "select",
    childView: UserOptionView
});

var MessageView = Marionette.View.extend({
    template: "#message-template",
    templateContext: {
        formatTsIsoDate: function(ts) {
            var dat = new Date(ts * 1000);
            var y = dat.getFullYear();
            var m = dat.getMonth() + 1;
            var d = dat.getDate();
            if (m < 10) m = "0" + m;
            if (d < 10) d = "0" + d;
            return y + "-" + m + "-" + d;
        },
        formatTs: function(ts) {
            var d = new Date(ts * 1000);
            var m = d.getMinutes();
            if (m < 10) m = "0" + m;
            return d.getHours() + ":" + m;
        }
    }
});
var MessagesView = Marionette.CollectionView.extend({

    childView: function(child) {
        return MessageView;
    },
    childViewOptions: function(model, index) {
        // do some calculations based on the model
        return {
            childIndex: "index"
        }
    }
});

var ContactView = Marionette.View.extend({
    className: "clickable",
    template: "#contact-template",
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

var ContactsView = Marionette.CollectionView.extend({
    childView: ContactView
});

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


var UserlistItemView = Marionette.View.extend({
    tagName: "li",
    template: _.template('<%= login %>'),
    triggers: {
        "click": "select"
    }
});

var UserlistView = Marionette.CollectionView.extend({
    tagName: "ul",
    childView: UserlistItemView
});


var AddContactView = Marionette.View.extend({
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
    template: "#addcontact-template",
    events: {
        "focus input": "autocomplete",
        "input input": "autocomplete",
        "click button": "select"
    },

    regions: {
        "auto": ".js-auto"
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

module.exports = Marionette.Application.extend({
    initialize: function() {
        Backbone.emulateHTTP = true;
        var me = this;
        this.contacts = new ContactCollection();
        this.messages = new MessageCollection();
        this.userMessages = new MessageCollection();
        this.listenTo(this.messages, "add", function(m) {
            //console.log("Add", m);
            //me.unreadRecalc();
            var selectedContact = me.getSelectedContact();
            if (selectedContact) {
                if (m.get("contact_id") === selectedContact.get("id")) {
                    this.userMessages.add(m);
                }
            }
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

        this.listenTo(this.contactsView, "childview:contact:select", function(e, v) {
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
            if (dat != prevDate) {
                m.set("dateSeparator", true);
                prevDate = dat;
            }
            if (!m.get("r") && (m.get("rxtx") == "rx")) uc++;
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

    unselect: function(contact) {
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
