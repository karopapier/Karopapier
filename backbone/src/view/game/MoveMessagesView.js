const Marionette = require('backbone.marionette');
const MoveMessageView = require('./MoveMessageView');
const TextView = require('../TextView');

module.exports = Marionette.CollectionView.extend({
    id: 'mmv',
    childView: MoveMessageView,
    emptyView: TextView,
    emptyViewOptions: {
        text: '--------- Keiner spricht, hier herrscht höchste Konzentration --------',
    },

    onRender: function() {
        let el = this.el;
        let parent = el.parentNode;
        if (parent) {
            parent.scrollTop = parent.scrollHeight;
        }
    },
});
