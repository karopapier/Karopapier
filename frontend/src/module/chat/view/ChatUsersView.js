const Marionette = require('backbone.marionette');
const UserView = require('../../user/view/UserView');

module.exports = Marionette.CollectionView.extend({
    tagName: 'ul',
    className: 'chat-users',
    childView: UserView,

    initialize() {
        // re-apply filter on change of chat property
        this.listenTo(this.collection, 'change:chat', (mo) => {
            this.removeFilter();
            this.setFilter(this.basicFilter);
        });

        this.setFilter(this.basicFilter);
    },

    basicFilter(model) {
        return model.get('chat');
    },

    childViewOptions: {
        tagName: 'li',
        withGames: true,
        withAnniversary: true,
        withDesperation: true,
        withGamesLink: true,
        withInfoLink: true,
    },
});
