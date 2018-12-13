const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const NotificationControlView = require('./NotificationControlView');

module.exports = Marionette.View.extend({
    template: require('../templates/userInfoBar.html'),

    regions: {
        'notification': '.header-notification-control',
    },

    initialize() {
        this.listenTo(this.model, 'change', this.render);
        this.notificationControl = dataChannel.request('notificationControl');
    },

    onRender() {
        console.log(this.notificationControl);
        this.showChildView('notification', new NotificationControlView({
            model: this.notificationControl,
        }));
    },
});
