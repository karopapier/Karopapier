'use strict';
const Marionette = require('backbone.marionette');
const MessageView = require('./MessageView');
module.exports = Marionette.CollectionView.extend({

    childView(child) {
        return MessageView;
    },

    childViewOptions(model, index) {
        // do some calculations based on the model
        return {
            childIndex: 'index',
        };
    },

    checkScroll() {
        const parent = this.$el.parent();
        const scrollhight = parent.prop('scrollHeight');
        const scrollpos = parent.scrollTop();
        const height = parent.height();
        // calculate
        const overflow = scrollhight - scrollpos - height;
        if (overflow < 80) {
            this.scrollDown();
            return;
        }
        console.info('Show new message indicator');
    },

    scrollDown() {
        const parent = this.$el.parent();
        parent.scrollTop(parent.prop('scrollHeight'));
    },

    onRender() {
        // Defer until all children are there
        setTimeout(() => {
            this.listenTo(this.collection, 'add', this.checkScroll);
            this.scrollDown();
        }, 0);
    },
});

