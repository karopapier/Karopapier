// const _ = require('underscore');
const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

// const $ = require('jquery');
// const emojione = require('emojione');

module.exports = Marionette.View.extend({
    tagName: 'div',
    className: 'chatMessage',
    template: require('../templates/chatMessage.html'),

    initialize() {
        this.linkifier = dataChannel.request('linkifier');
    },

    templateContext() {
        return {
            linkify: this.linkifier.linkify.bind(this.linkifier), // sonst kommt die message als 'this'
        };
    },

    /*
    id() {
        return 'cm' + this.model.get('lineId');
    },

    initialize(options) {
        _.bindAll(this, 'render');
        options = options || {};
        if (!options.util) {
            console.error('No util in ChatMessageView');
            return false;
        }
        this.util = options.util;

        // check if it is a botrix game message
        const bgreq = /Botrix, spiel mit/g;
        const bgack = /.*fahr ich jetzt in Grund und Boden!/g;
        const bgack2 = /.*mach ich jetzt Ruehrei/g;
        const bgack3 = /.*Direktlink/g;
        const line = this.model.get('text');
        if (line.match(bgreq) || line.match(bgack) || line.match(bgack2) || line.match(bgack3)) {
            this.model.set('isBotrixGameMessage', true);
            this.$el.addClass('botrixGame');
        }
        this.render();
        this.checkVisible();

        this.listenTo(this.model, 'remove', this.remove);
        this.listenTo(this.model, 'change:funny change:oldLink', this.updateText);
        this.listenTo(this.model, 'change:showBotrix', this.checkVisible);
    },

    updateText() {
        const me = this;
        const $dummy = $('<span>');
        $dummy.html(this.model.get('text'));
        let text = $dummy.text();

        text = emojione.unicodeToImage(text);
        text = this.util.linkify(text);

        const $textSpan = this.$el.find('.chatText').first();
        $textSpan.html(text);
        this.$el.find('img').load((e) => {
            const $parparent = me.$el.parent().parent();
            const newHeight = me.$el.height();
            // console.log("Message height changed from", messageHeight, "to", newHeight);
            const old = $parparent.scrollTop();
            const now = old + newHeight - messageHeight;
            $parparent.scrollTop(now);
            // console.log("nachher", $parparent.scrollTop());
        });
        let messageHeight = -1;
        setTimeout(() => {
            messageHeight = me.$el.height();
        }, 5);
    },

    checkVisible() {
        const s = this.model.get('showBotrix');
        const is = this.model.get('isBotrixGameMessage');
        if (is && !s) {
            this.$el.hide();
        } else {
            this.$el.show();
        }
    },

    render() {
        // var text = this.model.get("text");
        const data = this.model.toJSON();
        data.text = '';
        this.$el.html(this.template(data));

        this.updateText();
        return this;
    },
    */
});
