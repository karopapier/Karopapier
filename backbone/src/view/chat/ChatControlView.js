const _ = require('underscore');
const Backbone = require('backbone');
const NotificationControlView = require('../NotificationControlView');

module.exports = Backbone.View.extend({
    tagName: 'div',
    template: require('../../../templates/chat/chatControl.html'),
    initialize: function(options) {
        this.app = options.app;
        _.bindAll(this, 'render');
        this.listenTo(this.app.User, 'change:id', this.render);
        this.listenTo(this.model, 'change:limit', this.render);
        this.listenTo(this.model, 'change:start', this.render);
        this.listenTo(this.model, 'change:lastLineId', this.render);
        this.listenTo(this.model, 'change:history', this.render);
        this.listenTo(this.model, 'change:funny', this.updateFunny);
        this.listenTo(this.model, 'change:oldLink', this.updateOldLink);
        this.listenTo(this.model, 'change:showBotrix', this.updateBotrix);

        this.notificationControlView = new NotificationControlView({
            model: this.app.notificationControl,
        });
        return this;
    },
    events: {
        'click .messageLimit': 'setLimit',
        'change #startPicker': 'syncStart',
        'input #startPicker': 'syncStart',
        'click #startLineUpdate': 'setStart',
        'click .toggleTimewarp': 'toggleTimewarp',
        'click span.rewind': 'rewind',
        'click span.forward': 'forward',
        'click #funnyChat': 'setLinkifyFun',
        'click #oldLink': 'setOldLink',
        'click #showBotrix': 'setShowBotrix',
    },
    setStart: function(e) {
        let start = parseInt(this.$el.find('#startLine').val());
        this.model.set('start', start);
    },
    syncStart: function(e) {
        let v = parseInt(e.currentTarget.value);
        $('#startLine').val(v);
    },
    setLimit: function(e) {
        let limit = parseInt($(e.currentTarget).text());
        this.model.set('limit', limit);
    },
    rewind: function(e) {
        let start = this.model.get('start');
        if (start > 100) start -= 100;
        this.model.set('start', start);
    },
    forward: function(e) {
        let start = this.model.get('start');
        start += 100;
        this.model.set({
            start: start,
        });
    },
    toggleTimewarp: function(e) {
        let history = this.model.get('history');
        let settings = {};
        settings.history = !history;
        settings.limit = 100;
        if (history) {
            // switch to "normal"
            settings.limit = 20;
        } else {
            // switch to history
            settings.limit = 100;
        }

        // console.log(settings);
        this.model.set(settings);
    },

    setLinkifyFun: function(e) {
        let funny = $(e.currentTarget).prop('checked');
        this.model.set('funny', funny);
    },
    setShowBotrix: function(e) {
        let showBotrix = $(e.currentTarget).prop('checked');
        this.model.set('showBotrix', showBotrix);
    },
    setOldLink: function(e) {
        let oldLink = $(e.currentTarget).prop('checked');
        this.model.set('oldLink', oldLink);
    },
    updateFunny: function(e) {
        this.$el.find('#funnyChat').prop('checked', this.model.get('funny'));
    },
    updateOldLink: function(e) {
        this.$el.find('#oldLink').prop('checked', this.model.get('oldLink'));
    },
    updateBotrix: function(e) {
        this.$el.find('#showBotrix').prop('checked', this.model.get('showBotrix'));
    },
    render: function() {
        console.log('Render control view', this.model.get('start'), this.model.get('lastLineId'));
        if (this.app.User.get('id') != 0) {
            this.$el.html(this.template({user: this.app.User.toJSON(), settings: this.model.toJSON()}));

            this.notificationControlView.setElement(this.$('#notificationControlView')).render();
        } else {
            this.$el.html('Nicht angemeldet');
        }
        return this;
    },
});