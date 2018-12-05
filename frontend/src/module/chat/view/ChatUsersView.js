const Marionette = require('backbone.marionette');
const UserView = require('../../user/view/UserView');

module.exports = Marionette.NextCollectionView.extend({
    tagName: 'ul',
    className: 'chat-users',
    childView: UserView,

    initialize() {
        // re-apply filter on change of chat property
        this.listenTo(this.collection, 'change:chat', this.filter);
    },

    viewFilter(view) {
        return view.model.get('chat');
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
