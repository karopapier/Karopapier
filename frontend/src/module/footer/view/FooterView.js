const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const ChatMessageView = require('../../../module/chat/view/ChatMessageView');

module.exports = Marionette.View.extend({

    template: require('../templates/footer.html'),
    initialize() {
        this.chatMessages = dataChannel.request('chatMessages');
        this.listenTo(this.chatMessages, 'add', this.render);
    },
    regions: {
        chatMessage: '.footer-chat-message',
    },
    onRender() {
        this.chatMessages.getLoadedPromise().then(() => {
            this.showChildView('chatMessage', new ChatMessageView({
                model: this.chatMessages.getLast(),
            }));
        });
    },
});
