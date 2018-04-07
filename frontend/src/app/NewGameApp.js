const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

// Collections
const LobbyUserCollection = require('../collection/LobbyUserCollection');

// Models
const LobbyUserFilter = require('../model/newgame/LobbyUserFilter');

// Views
const LobbyUserFilterView = require('../view/newgame/LobbyUserFilterView');
const LobbyUsersView = require('../view/newgame/LobbyUsersView');
const NewGameLayout = require('../layout/NewGameLayout');

module.exports = Marionette.Application.extend({

    initialize(config) {
        console.log('Init NewGame App');

        this.layout = new NewGameLayout({});

        this.loadInitialAndStart();
    },

    loadInitialAndStart() {
        this.users = dataChannel.request('users');
        this.users.getLoadedPromise().then(() => {
            this.lobbyUsers = new LobbyUserCollection(this.users.toJSON());
            this.start();
        });
    },

    start() {
        console.info('Start NewGame App');
        this.lobbyUserFilter = new LobbyUserFilter();
        this.layout.getRegion('playerfilter').show(new LobbyUserFilterView({
            model: this.lobbyUserFilterr
        }));

        this.layout.getRegion('playerlist').show(new LobbyUsersView({
            collection: this.lobbyUsers,
            filterModel: this.lobbyUserFilter
        }));
    }
});
