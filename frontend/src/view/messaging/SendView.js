const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/messaging/sendview.html'),
    tagName: 'form',
    events: {
        submit: 'send',
        keyup: 'resize',
    },

    onRender: function() {
        const me=this;
        setTimeout(function() {
            me.resize();
        }, 100);
    },

    send: function(e) {
        const text = this.$('.send-text').val().trim();
        if (text.length === 0) {
            e.preventDefault();
            return false;
        }
        const userId = this.model.get('id');
        console.info('Nachricht', text, 'an', userId);

        this.trigger('send', {
            userId: userId,
            text: text,
        });
        this.$('input[type=submit]').prop('disabled', true);
        e.preventDefault();
    },

    reset: function() {
        this.$('.send-text').val('');
        this.enable();
    },

    enable: function() {
        this.$('input[type=submit]').prop('disabled', false);
    },

    resize: function() {
        const o = this.$('.send-text')[0];
        o.style.height = '1px';
        o.style.height = (o.scrollHeight)+'px';
    },
});

