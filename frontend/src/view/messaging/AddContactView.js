const Marionette = require('backbone.marionette');
const UserCollection = require('../../collection/UserCollection');
const UserlistView = require('./UserlistView');
module.exports = Marionette.View.extend({
    template: require('../../../templates/messaging/addContact.html'),

    events: {
        'focus input': 'autocomplete',
        'input input': 'autocomplete',
        'click button': 'select',
    },

    regions: {
        auto: '.js-auto',
    },

    initialize: function() {
        let me = this;
        this.filteredUsers = new UserCollection(this.collection.toJSON());
        this.listview = new UserlistView({
            collection: this.filteredUsers,
        });
        this.listenTo(this.listview, 'childview:select', function(e) {
            me.insert(e.model);
            me.unautocomplete();
        });
        this.listview.$el.css({
            maxHeight: 60,
            overflow: 'auto',
            backgroundColor: 'white',
            border: '1px solid black',
        });
    },
    autocomplete: function(e) {
        let typed = this.$('input').val().toLowerCase();
        this.filteredUsers.reset(this.collection.filter(function(m) {
            return !m.get('login').toLowerCase().indexOf(typed);
        }));
        this.listview = this.getRegion('auto').show(this.listview);
        this.getRegion('auto').$el.show();
    },

    unautocomplete: function() {
        this.getRegion('auto').$el.hide();
    },

    insert: function(m) {
        this.$('input').val(m.get('login'));
    },

    select: function() {
        this.trigger('select', this.$('input').val());
    },
});

