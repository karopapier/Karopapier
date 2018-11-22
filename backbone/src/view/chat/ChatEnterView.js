const $ = require('jquery');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    tagName: 'div',
    template: window['JST']['chat/chatEnter'],
    initialize(options) {
        this.listenTo(this.model, 'change:id', this.render);
        return this;
    },
    events: {
        'submit': 'sendMessage',
    },
    sendMessage(e) {
        e.preventDefault();
        const msg = $('#newchatmessage').val();
        if (msg != '') {
            $.ajax({
                url: '/api/chat/message.json',
                type: 'POST',
                method: 'POST',
                crossDomain: true,
                // better than data: "msg=" + msg as it works with ???? as well
                contentType: 'application/json',
                data: JSON.stringify({msg}),
                xhrFields: {
                    withCredentials: true,
                },
                success: function sendMessageSuccess(data) {
                    $('#newchatmessage').val('');
                    $('#newchatmessagesubmit').prop('disabled', false).stop().animate({opacity: 1});
                },
                error(xhr, status) {
                    console.error(status, xhr);
                    $('#newchatmessagesubmit').prop('disabled', false).stop().animate({opacity: 1});
                },
            });
            $('#newchatmessagesubmit').prop('disabled', true).stop().animate({opacity: 0});
        }
    },
    render() {
        const uid = this.model.get('id');
        let html = '';
        if (uid < 0) {
            html = 'Wart mal, kenn ich Dich?';
        } else if (uid == 0) {
            html = 'Du bist nicht angemeldet...';
        } else {
            html = this.template({user: this.model.toJSON()});
        }
        this.$el.html(html);
        return this;
    },
});
