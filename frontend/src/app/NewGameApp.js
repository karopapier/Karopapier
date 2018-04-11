const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const appChannel = Radio.channel('app');
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
const MapInfoView = require('../view/map/MapInfoView');

module.exports = Marionette.Application.extend({

    initialize(config) {
        console.log('Init NewGame App');

        this.layout = new NewGameLayout({});
        this.authUser = dataChannel.request('user:logged:in');

        this.loadInitialAndStart();

        appChannel.on('lobbyuser:select:toggle', (id) => {
            this.selectToggle(id);
        });
    },

    loadInitialAndStart() {
        this.users = dataChannel.request('users');
        this.users.getLoadedPromise().then(() => {
            this.lobbyUsers = new LobbyUserCollection(this.users.toJSON());
            this.lobbyUsers.comparator = (model1, model2) => {
                const a = model1.get('login').substr(0, 1).toLowerCase();
                const b = model2.get('login').substr(0, 1).toLowerCase();
                if (a < b) return -1;
                if (a === b) return 0;
                return 1;
            };
            this.selectedUsers = new LobbyUserCollection();
            this.selectToggle(this.authUser.get('id'), true);
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
            collection: this.selectedUsers,
            map: this.map
        }));

        this.mapView = new MapCanvasView({
            model: this.map
        });
        this.mapView.settings.set({size: 1, border: 0});
        this.layout.getRegion('mapcanvas').show(this.mapView);

        this.listenTo(this.layout, 'map:change', (view, e) => {
            this.map.setId(e.currentTarget.value);
        });

        this.layout.getRegion('mapinfo').show(new MapInfoView({
            model: this.map
        }));
    },

    selectToggle(id, force) {
        if (!force) {
            // cannot add/deselect myself
            if (id === this.authUser.get('id')) return;
        }

        const u = this.lobbyUsers.get(id);
        u.set('selected', !u.get('selected'));

        this.selectedUsers.add(u);
    }
});
