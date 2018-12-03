const Marionette = require('backbone.marionette');
const UserView = require('../../user/view/UserView');

module.exports = Marionette.CollectionView.extend({
    tagName: 'ul',
    className: 'chat-users',
    childView: UserView,

    initialize() {
        this.listenTo(this.collection, 'update', () => {
            this.render();
        });
    },

    filter(model, index, collection) {
        // only those with chat flag set
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
