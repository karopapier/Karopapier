const Marionette = require('backbone.marionette');
const ChatLayout = require('../layout/ChatLayout');
const DranLayout = require('../layout/DranLayout');
const DranAppView = require('../view/DranAppView');
const GameCollectionView = require('../view/game/GameCollectionView');
const GameListItemView = require('../view/game/GameListItemView');

module.exports = Marionette.Application.extend({
    className: 'dranApp',
    initialize: function() {
        this.layout = new ChatLayout({});
        this.layout = new DranLayout({});
        this.view = new DranAppView({
            model: this,
        });
        this.gamesView = new GameCollectionView({
            childView: GameListItemView,
            collection: Karopapier.UserDranGames,
        });
    },
});
