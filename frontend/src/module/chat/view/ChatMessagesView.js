const Marionette = require('backbone.marionette');
const ChatMessageView = require('./ChatMessageView');

module.exports = Marionette.CollectionView.extend({
    tagName: 'div',
    id: 'chatMessagesContainer',
    childView: ChatMessageView,


    /*
    initialize(options) {
        options = options || {};
        if (!options.util) {
            console.error('No util in ChatMessagesView');
            return false;
        }
        this.util = options.util;
        _.bindAll(this, 'addItem', 'scrollCheck');
        this.collection.on('add', this.addItem);
        this.currentStart = 0;
        this.currentEnd = 0;
    },
    scrollcheck() {
        console.log('I scroll');
        const $c = $('#chatMessages');
        const topf = $c.prop('scrollTop');
        const hoch = $c.prop('scrollHeight');
        console.log(topf, hoch);
    },
    addItem(chatMessage, animated) {
        const chatMessageView = new ChatMessageView({
            model: chatMessage,
            util: this.util,
        });
        const lineId = parseInt(chatMessage.get('lineId'));

        // find out where to insert the template
        const previousMessage = this.$el.find('#cm' + (lineId - 1));

        // chatMessageView.$el.find("img").on("load", this.scrollDown.bind(this));

        // keep track of scroll
        const $parent = this.$el.parent();
        const sh = $parent.prop('scrollHeight');
        const st = $parent.scrollTop();

        // add message at right place, either at beginning or after previous one
        if (previousMessage[0]) {
            previousMessage.after(chatMessageView.$el);
        } else {
            this.$el.prepend(chatMessageView.$el);
        }
        const newSh = $parent.prop('scrollHeight');

        // find how much the height changed and scroll to original position
        $parent.scrollTop(st + newSh - sh);
    },
    removeItem(cm) {
        console.log(cm.get('lineId'), 'removed');
    },
    scrollDown(options) {
        options = _.defaults(options || {}, {forced: false, animated: true});
        // check if scrolled down
        const $parent = this.$el.parent();
        const toScrollDown = $parent.prop('scrollHeight') - $parent.prop('clientHeight') - $parent.prop('scrollTop');
        // user is scrolled up, don't follow new line
        if ((toScrollDown > 40) && !(options.forced)) {
            // console.log("Skip scroll");
            return false;
        }
        // console.log("Ich scrolle",$parent.prop('scrollHeight') );
        // setTimeout(function() {

        /*
         options.animated=false;
         //$el.animate({ scrollTop: $el.prop('scrollHeight') }, 1000);
         if (options.animated) {
         $parent.stop().animate({scrollTop: $parent.prop("scrollHeight")}, 100);
         } else {
         $parent.stop().scrollTop($parent.prop("scrollHeight") - $parent.height());
         }
         //},100);
         */

    /*
        setTimeout(() => {
            $parent.stop().animate({scrollTop: $parent.prop('scrollHeight')}, 100);
        }, 10);
    },
    scrollCheck() {
        const $parent = this.$el.parent();
        const top = $parent.prop('scrollTop'); // how much space until you reach the top
        if (top <= 100) {
            this.trigger('CHAT:MESSAGES:TOP');
        }
    },
    */
});

/*
 #var toScrollDown = $parent.prop("scrollHeight") - $parent.prop("clientHeight") - $parent.prop("scrollTop");
 function wieweitunten() {
 var $c=$('#chatMessages');
 var topf=$c.prop("scrollTop");
 var hoch = $c.prop("scrollHeight");
 console.log(topf, hoch);
 }
 */
