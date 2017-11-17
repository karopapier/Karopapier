'use strict';
const Backbone = require('backbone');
const Radio = require('backbone.radio');
const Marionette = require('backbone.marionette');
const $ = require('jquery');

// Layout
const MessagingLayout = require('../layout/MessagingLayout');

// Model
const Message = require('../model/Message');

// Collections
const MessageCollection = require('../collection/MessageCollection');
const ContactCollection = require('../collection/ContactCollection');

// Views
const SendView = require('../view/messaging/SendView');
const ContactDetailsView = require('../view/messaging/ContactDetailsView');
const MessagesView = require('../view/messaging/MessagesView');
const ContactsView = require('../view/messaging/ContactsView');
const AddContactView = require('../view/messaging/AddContactView');
const MessagingRouter = require('../router/MessagingRouter');

module.exports = window.MessagingApp = Marionette.Application.extend({
    initialize(config) {
        let me = this;

        this.config = config;

        const dataChannel = Radio.channel('data');
        this.authUser = dataChannel.request('user:logged:in');

        this.users = dataChannel.request('users');
        this.contacts = new ContactCollection();
        this.messages = new MessageCollection();
        this.userMessages = new MessageCollection();
        this.listenTo(this.messages, 'add', (m) => {
            let selectedContact = me.getSelectedContact();
            // if current contact selected, add message to filtered userMessages
            if (selectedContact) {
                if (m.get('contact_id') === selectedContact.get('id')) {
                    this.userMessages.add(m);
                }
            }
        });

        this.messagingChannel = Radio.channel('messaging');
        this.messagingChannel.on('message:new', (data) => {
            let m = new Message(data);
            let selectedContact = this.getSelectedContact();
            // if current contact selected, add message as already read
            if (selectedContact) {
                if (m.get('contact_id') === selectedContact.get('id')) {
                    m.set('r', 1);
                }
            }
            this.messages.add(m);
        });

        this.layout = new MessagingLayout({
            triggers: {
                'click .backnav': 'unselect'
            }
        });

        this.loadInitialAndStart();
    },

    loadInitialAndStart() {
        let me = this;
        $.when(
            me.contacts.fetch(),
            me.messages.fetch()
        ).done(function() {
            me.start();
        });
    },

    start() {
        console.info('Start App');
        let me = this;
        this.unreadRecalc();

        // Now bind to add in case of realtime updates
        this.listenTo(this.messages, 'add', this.unreadRecalc);

        this.contactsView = new ContactsView({
            collection: this.contacts
        });

        this.layout.getRegion('contacts').show(this.contactsView);

        this.listenTo(this.layout, 'unselect', function() {
            me.unselect();
        });

        this.listenTo(this.contactsView, 'childview:contact:select', function(e) {
            let contact = e.model;
            me.select(contact);
        });

        this.addContactView = new AddContactView({
            viewComparator: 'login',
            collection: this.users
        });
        this.layout.getRegion('addcontact').show(this.addContactView);
        this.addContactView.on('select', function(contactName) {
            me.selectName(contactName);
        });

        this.router = new MessagingRouter({
            app: this
        });
    },

    selectName: function(contactName) {
        console.info('Select', contactName);
        let c = this.contacts.findWhere({
            login: contactName
        });
        if (!c) {
            c = this.users.findWhere({
                login: contactName
            });

            if (!c) {
                console.warn('Not found');
                return false;
            }
            this.contacts.add(c);
        }

        this.select(c);
    },

    select: function(contact) {
        let me = this;
        this.contacts.each(function(c) {
            c.set('selected', contact.get('id') === c.get('id'));
        });
        let messages = this.messages.where({
            contact_id: contact.get('id')
        });
        let prevDate = '';
        let uc = 0;
        messages.forEach(function(m) {
            let d = new Date(m.get('ts') * 1000);
            let dat = '' + d.getDate() + d.getMonth();
            if (dat !== prevDate) {
                m.set('dateSeparator', true);
                prevDate = dat;
            }
            if (!m.get('r') && (m.get('rxtx') === 'rx')) uc++;
        });
        this.userMessages.reset(messages);
        this.layout.getRegion('messages').show(new MessagesView({
            collection: this.userMessages
        }));
        this.sendView = new SendView({
            model: contact
        });
        this.layout.getRegion('send').show(this.sendView);
        this.layout.$el.addClass('js-selected');
        this.layout.$el.removeClass('js-unselected');
        this.listenTo(this.sendView, 'send', function(data) {
            me.messages.create(data, {
                wait: true,
                success: function() {
                    me.sendView.reset();
                },
                error: function() {
                    me.sendView.enable();
                    // Fehler beim Versand
                }
            });
        });

        this.layout.getRegion('contactInfo').show(new ContactDetailsView({
            model: contact
        }));

        if (uc > 0) {
            contact.setAllRead();
            this.unreadRecalc();
        }

        Backbone.history.navigate('zettel/' + contact.get('login'));
    },

    unselect: function() {
        this.contacts.each(function(c) {
            c.set('selected', false);
        });
        this.userMessages.reset([]);
        this.layout.$el.addClass('js-unselected');
        this.layout.$el.removeClass('js-selected');
        this.layout.getRegion('send').empty();
        this.layout.getRegion('contactInfo').empty();
        Backbone.history.navigate('zettel');
    },

    unreadRecalc: function() {
        let total = 0;
        console.info('Unread recalc');
        let me = this;
        this.contacts.each(function(c) {
            let uc = me.messages.where({
                r: 0,
                contact_id: c.get('id'),
                rxtx: 'rx'
            });
            c.set('uc', uc.length);
            total += uc.length;
        });
        this.authUser.set('uc', total);
    },

    getSelectedContact: function() {
        return this.contacts.findWhere({selected: true});
    }
});
