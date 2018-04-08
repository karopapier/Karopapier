const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

// Collections
const LobbyUserCollection = require('../collection/LobbyUserCollection');

// Models
const LobbyUserFilter = require('../model/newgame/LobbyUserFilter');
const KaroMap = require('../model/map/KaroMap');

// Views
const LobbyUserFilterView = require('../view/newgame/LobbyUserFilterView');
const LobbyUsersView = require('../view/newgame/LobbyUsersView');
const NewGameLayout = require('../layout/NewGameLayout');
const SelectedUsersView = require('../view/newgame/SelectedUsersView');
const MapCanvasView = require('../view/map/MapCanvasView');

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
        this.map = new KaroMap(1);
    },

    start() {
        console.info('Start NewGame App');
        this.lobbyUserFilter = new LobbyUserFilter();
        this.layout.getRegion('playerfilter').show(new LobbyUserFilterView({
            model: this.lobbyUserFilter
        }));

        this.layout.getRegion('playerlist').show(new LobbyUsersView({
            collection: this.lobbyUsers,
            filterModel: this.lobbyUserFilter
        }));

        this.layout.getRegion('selectedlist').show(new SelectedUsersView({
            collection: this.lobbyUsers
        }));

        this.mapView = new MapCanvasView({
            model: this.map
        });
        this.mapView.settings.set({size: 1, border: 0});
        this.layout.getRegion('mapcanvas').show(this.mapView);
    }
});
