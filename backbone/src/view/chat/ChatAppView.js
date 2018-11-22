const Marionette = require('backbone.marionette');
const $ = require('jquery');

module.exports = Marionette.ItemView.extend({
    className: 'chatAppView',
    render() {
        this.model.layout.render();

        // insert views
        this.model.layout.chatMessages.show(this.model.chatMessagesView, {preventDestroy: true});
        this.model.layout.chatInfo.show(this.model.chatInfoView, {preventDestroy: true});
        this.model.layout.chatControl.show(this.model.chatControlView, {preventDestroy: true});
        this.model.layout.chatEnter.show(this.model.chatEnterView, {preventDestroy: true});

        this.$el.html(this.model.layout.$el);
        let $el = this.model.layout.chatMessages.$el;
        $($el).on('scroll', this.model.chatMessagesView.scrollCheck);
    },
});
