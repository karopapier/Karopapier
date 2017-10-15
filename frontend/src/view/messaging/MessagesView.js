var Marionette = require('backbone.marionette');
var MessageView = require('./MessageView');
module.exports = Marionette.CollectionView.extend({

    childView: function(child) {
        return MessageView;
    },

    childViewOptions: function(model, index) {
        // do some calculations based on the model
        return {
            childIndex: "index"
        }
    }
});

