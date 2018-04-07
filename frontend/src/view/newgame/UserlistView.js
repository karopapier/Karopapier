const Marionette = require('backbone.marionette');
module.exports = Marionette.CollectionView.extend({
    initialize() {
        this.userFilter = this.getOption('modelFilter');
    }
});
