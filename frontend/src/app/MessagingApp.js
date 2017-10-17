'use strict';
const Backbone = require('backbone');
const Radio = require('backbone.radio');
const Marionette = require('backbone.marionette');

// Layout
const MessagingLayout = require('../layout/MessagingLayout');

// Model
const User = require('../model/User');
const Message = require('../model/Message');

const KEvIn = require('../model/KEvIn');

// Collections
const MessageCollection = require('../collection/MessageCollection');
const UserCollection = require('../collection/UserCollection');
const ContactCollection = require('../collection/ContactCollection');

// Views
const SendView = require('../view/messaging/SendView');
const ContactDetailsView = require('../view/messaging/ContactDetailsView');
const MessagesView = require('../view/messaging/MessagesView');
const ContactsView = require('../view/messaging/ContactsView');
const AddContactView = require('../view/messaging/AddContactView');
const UserInfoBarView = require('../view/UserInfoBarView');

window.USERS = new UserCollection();

const MessagingRouter = Backbone.Router.extend({
    initialize: function(options) {
        this.app = options.app;
    },
    routes: {
        'zettel/:contact': 'select',
        'zettel': 'index'
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
        let me = this;
        this.authUser = new User();
        this.authUser.url = '/api/users/check';
        this.authUser.fetch();

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

        this.dataProvider = Radio.channel('data');
        this.dataProvider.reply('user:logged:in', function() {
            return me.authUser;
        });

        this.dataProvider.reply('config', () => {
            return {
                host: '//ws01.panamapapier.de'
            };
        });

        this.kevin = new KEvIn();

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

        this.messagingLayout = new MessagingLayout({
            el: '#messaging',
            triggers: {
                'click .backnav': 'unselect'
            }
        });
    },

    start: function() {
        console.info('Start App');
        let me = this;
        this.unreadRecalc();

        // Now bind to add in case of realtime updates
        this.listenTo(this.messages, 'add', this.unreadRecalc);

        this.messagingLayout.render();

        this.contactsView = new ContactsView({
            collection: this.contacts
        });

        this.messagingLayout.getRegion('contacts').show(this.contactsView);

        this.listenTo(this.messagingLayout, 'unselect', function() {
            me.unselect();
        });

        this.listenTo(this.contactsView, 'childview:contact:select', function(e) {
            let contact = e.model;
            me.select(contact);
        });

        this.addContactView = new AddContactView({
            viewComparator: 'login',
            collection: USERS
        });
        this.messagingLayout.getRegion('addcontact').show(this.addContactView);
        this.addContactView.on('select', function(contactName) {
            me.selectName(contactName);
        });

        this.userInfoBar = new UserInfoBarView({
            el: '#userInfoBar',
            model: this.authUser
        });
        this.userInfoBar.render();

        this.router = new MessagingRouter({
            app: this
        });
        Backbone.history.start({pushState: true});
    },

    selectName: function(contactName) {
        console.info('Select', contactName);
        let c = this.contacts.findWhere({
            login: contactName
        });
        if (!c) {
            c = USERS.findWhere({
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
        this.messagingLayout.getRegion('messages').show(new MessagesView({
            collection: this.userMessages
        }));
        this.sendView = new SendView({
            model: contact
        });
        this.messagingLayout.getRegion('send').show(this.sendView);
        this.messagingLayout.$el.addClass('js-selected');
        this.messagingLayout.$el.removeClass('js-unselected');
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

        this.messagingLayout.getRegion('contactInfo').show(new ContactDetailsView({
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
        this.messagingLayout.$el.addClass('js-unselected');
        this.messagingLayout.$el.removeClass('js-selected');
        this.messagingLayout.getRegion('send').empty();
        this.messagingLayout.getRegion('contactInfo').empty();
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
