const Marionette = require('backbone.marionette');
const MessageView = require('./MessageView');
module.exports = Marionette.CollectionView.extend({

    childView(child) {
        return MessageView;
    },

    childViewOptions(model, index) {
        // do some calculations based on the model
        return {
            childIndex: 'index'
        };
    }
});

