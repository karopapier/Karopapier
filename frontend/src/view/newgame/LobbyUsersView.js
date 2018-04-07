const Marionette = require('backbone.marionette');
module.exports = Marionette.CollectionView.extend({
    childView: require('./LobbyUserView'),

    initialize() {
        this.userFilter = this.getOption('filterModel');
        this.listenTo(this.userFilter, 'change:desperate change:login', this.render);
    },

    filter(model, index, collection) {
        if (this.userFilter.get('desperate')) {
            if (!(model.get('desperate'))) {
                return false;
            }
        }

        const loginFilter = this.userFilter.get('login');
        if (loginFilter.length > 0) {
            const x = new RegExp(loginFilter, 'i');
            return model.get('login').match(x);
        }

        return true;
    }
});
