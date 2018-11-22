const $ = require('jquery');
const _ = require('underscore');
const Backbone = require('backbone');
const KaroNotificationView = require('./KaroNotificationView');

module.exports = Backbone.View.extend(/** @lends KaroNotifierView */ {
    tagName: 'ul',
    className: 'notifier',
    initialize() {
        _.bindAll(this, 'showNotification', 'render');
        // this.render();
        this.listenTo(this.model.notifications, 'add', this.showNotification);
    },
    showNotification(notification) { // , notifications) {
        const nv = new KaroNotificationView({
            model: notification,
        }).render();
        nv.el.style.display = 'none';
        nv.el.style.overflow = 'hidden';
        this.$el.append(nv.el);
        nv.$el.show({
            effect: 'fade',
        });
    },
    render() {
        $('body').append(this.el);
        return this;
    },
});
