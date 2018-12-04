const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const promiseChannel = Radio.channel('promise');

module.exports = Marionette.View.extend({
    tagName: 'div',
    template: require('../templates/chatEnter.html'),
    templateContext() {
        return {
            user: dataChannel.request('user:logged:in'),
        };
    },

    ui: {
        input: 'input',
        button: 'button',
    },

    initialize() {
        this.listenTo(this.model, 'change:id', this.render);
    },

    events: {
        'submit': 'sendMessage',
    },
    sendMessage(e) {
        e.preventDefault();
        const text = this.getUI('input').val();
        promiseChannel.request('send:message', text).then((msg) => {
            this.getUI('input').val('');
        }).catch((status, err) => {
            console.error(status, err);
        }).then(() => {
            this.getUI('button').prop('disabled', false);
        });

        this.getUI('button').prop('disabled', true);
    },
    /*
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
    */
});
