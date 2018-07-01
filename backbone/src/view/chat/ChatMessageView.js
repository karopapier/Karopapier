const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    tagName: 'div',
    className: 'chatMessage',
    template: require('../../../templates/chat/chatMessage.html'),
    id: function() {
        return 'cm' + this.model.get('lineId');
    },

    initialize: function(options) {
        _.bindAll(this, 'render');
        options = options || {};
        if (!options.util) {
            console.error('No util in ChatMessageView');
            return false;
        }
        this.util = options.util;

        // check if it is a botrix game message
        let bgreq = /Botrix, spiel mit/g;
        let bgack = /.*fahr ich jetzt in Grund und Boden!/g;
        let bgack2 = /.*mach ich jetzt Ruehrei/g;
        let bgack3 = /.*Direktlink/g;
        let line = this.model.get('text');
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

    updateText: function() {
        let me = this;
        let $dummy = $('<span>');
        $dummy.html(this.model.get('text'));
        let text = $dummy.text();

        text = emojione.unicodeToImage(text);
        text = this.util.linkify(text);

        let $textSpan = this.$el.find('.chatText').first();
        $textSpan.html(text);
        this.$el.find('img').load(function(e) {
            let $parparent = me.$el.parent().parent();
            let newHeight = me.$el.height();
            // console.log("Message height changed from", messageHeight, "to", newHeight);
            let old = $parparent.scrollTop();
            let now = old + newHeight - messageHeight;
            $parparent.scrollTop(now);
            // console.log("nachher", $parparent.scrollTop());
        });
        let messageHeight = -1;
        setTimeout(function() {
            messageHeight = me.$el.height();
        }, 5);
    },

    checkVisible: function() {
        let s = this.model.get('showBotrix');
        let is = this.model.get('isBotrixGameMessage');
        if (is && !s) {
            this.$el.hide();
        } else {
            this.$el.show();
        }
    },

    render: function() {
        // var text = this.model.get("text");
        let data = this.model.toJSON();
        data.text = '';
        this.$el.html(this.template(data));

        this.updateText();
        return this;
    },
});
