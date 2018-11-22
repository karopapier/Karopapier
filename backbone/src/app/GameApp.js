const Marionette = require('backbone.marionette');
const GameLayout = require('../layout/GameLayout');
const GameAppView = require('../view/game/GameAppView');
const GameDranQueueView = require('../view/game/DranQueueView');

module.exports = Marionette.Application.extend({
    className: 'gameApp',
    initialize(options) {
        this.app = options.app; // Karopapier
        this.settings = options.settings;

        this.layout = new GameLayout({});
        this.view = new GameAppView({
            model: this,
        });

        this.gameDranQueueView = new GameDranQueueView({
            collection: this.app.UserDranGames,
        });

        // make sure that (if available) the first two games in queue get/are always fully loaded with details
        this.listenTo(this.app.UserDranGames, 'update', () => {
            console.log('Q update');
            const q = this.app.UserDranGames;
            q.first(2).forEach((g) => {
                if (!g.get('completed')) g.load();
            });
            // var g = me.app.UserDranGames.at(0);
        });

        this.app.vent.on('GAME:MOVE', (data) => {
            // console.info(data);
        });
    },
});
