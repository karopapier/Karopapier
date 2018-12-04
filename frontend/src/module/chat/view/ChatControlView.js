const Marionette = require('backbone.marionette');
// const NotificationControlView = require('../../../../../backbone/src/view/NotificationControlView');

module.exports = Marionette.View.extend({
    tagName: 'div',
    template: require('../templates/chat-control.html'),

    initialize() {
        this.listenTo(this.model, 'change:limit', this.render);
    },

    ui: {
        'toggle-details': '.js-toggle-details',
        'details': '.js-chat-settings-details',
        'limits': '.js-chat-message-limit-selector',
    },

    events: {
        'click @ui.toggle-details': 'toggleDetails',
        'click @ui.limits': 'setLimit',
    },

    toggleDetails() {
        this.getUI('details').toggleClass('hidden');
    },

    setLimit(e) {
        this.model.set('limit', e.target.getAttribute('data-limit'));
    },

    /*
    initialize(options) {
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
    setStart(e) {
        const start = parseInt(this.$el.find('#startLine').val());
        this.model.set('start', start);
    },
    syncStart(e) {
        const v = parseInt(e.currentTarget.value);
        $('#startLine').val(v);
    },
    setLimit(e) {
        const limit = parseInt($(e.currentTarget).text());
        this.model.set('limit', limit);
    },
    rewind(e) {
        let start = this.model.get('start');
        if (start > 100) start -= 100;
        this.model.set('start', start);
    },
    forward(e) {
        let start = this.model.get('start');
        start += 100;
        this.model.set({
            start,
        });
    },
    toggleTimewarp(e) {
        const history = this.model.get('history');
        const settings = {};
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

    setLinkifyFun(e) {
        const funny = $(e.currentTarget).prop('checked');
        this.model.set('funny', funny);
    },
    setShowBotrix(e) {
        const showBotrix = $(e.currentTarget).prop('checked');
        this.model.set('showBotrix', showBotrix);
    },
    setOldLink(e) {
        const oldLink = $(e.currentTarget).prop('checked');
        this.model.set('oldLink', oldLink);
    },
    updateFunny(e) {
        this.$el.find('#funnyChat').prop('checked', this.model.get('funny'));
    },
    updateOldLink(e) {
        this.$el.find('#oldLink').prop('checked', this.model.get('oldLink'));
    },
    updateBotrix(e) {
        this.$el.find('#showBotrix').prop('checked', this.model.get('showBotrix'));
    },
    render() {
        console.log('Render control view', this.model.get('start'), this.model.get('lastLineId'));
        if (this.app.User.get('id') != 0) {
            this.$el.html(this.template({user: this.app.User.toJSON(), settings: this.model.toJSON()}));

            this.notificationControlView.setElement(this.$('#notificationControlView')).render();
        } else {
            this.$el.html('Nicht angemeldet');
        }
        return this;
    },
    */
});
