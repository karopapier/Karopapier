const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const appChannel = Radio.channel('app');
const dataChannel = Radio.channel('data');
const layoutChannel = Radio.channel('layout');

// Collections
const LobbyUserCollection = require('../collection/LobbyUserCollection');

// Models
const LobbyUserFilter = require('../model/newgame/LobbyUserFilter');
const NewGame = require('../model/newgame/NewGame');

// Views
const LobbyUserFilterView = require('../view/newgame/LobbyUserFilterView');
const LobbyUsersView = require('../view/newgame/LobbyUsersView');
const NewGameLayout = require('../layout/NewGameLayout');
const SelectedUsersView = require('../view/newgame/SelectedUsersView');
const MapImageView = require('../view/map/MapImageView');
const MapInfoView = require('../view/map/MapInfoView');
const MapSelectionView = require('../view/newgame/MapSelectionView');
const GameNameView = require('../view/newgame/GameNameView');

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
        this.maps = dataChannel.request('maps');
        this.maps.fetch();
        Promise.all([
            this.users.getLoadedPromise(),
            this.maps.getLoadedPromise(),
        ]).then(() => {
            this.lobbyUsers = new LobbyUserCollection(this.users.toJSON());
            this.lobbyUsers.comparator = (model1, model2) => {
                const a = model1.get('login').substr(0, 1).toLowerCase();
                const b = model2.get('login').substr(0, 1).toLowerCase();
                if (a < b) return -1;
                if (a === b) return 0;
                return 1;
            };
            this.selectedUsers = new LobbyUserCollection();
            const u = this.lobbyUsers.get(this.authUser.get('id'));
            this.select(u);

            this.map = this.maps.get(1);
            this.start();
        });
    },

    start() {
        console.info('Start NewGame App');

        this.game = new NewGame();

        this.layout.getRegion('name').show(new GameNameView({
            model: this.game,
        }));

        this.lobbyUserFilter = new LobbyUserFilter();
        this.layout.getRegion('playerfilter').show(new LobbyUserFilterView({
            model: this.lobbyUserFilter,
        }));

        this.layout.getRegion('playerlist').show(new LobbyUsersView({
            collection: this.lobbyUsers,
            filterModel: this.lobbyUserFilter,
        }));

        this.layout.getRegion('selectedlist').show(new SelectedUsersView({
            collection: this.selectedUsers,
            map: this.map,
        }));

        this.mapView = new MapImageView({
            model: this.map,
        });
        this.layout.getRegion('mapcanvas').show(this.mapView);

        this.listenTo(this.layout, 'map:change', (view, e) => {
            const map = this.maps.get(e.currentTarget.value);
            if (map) {
                this.map.set(map.toJSON());
            }
        });

        this.listenTo(this.layout, 'map:selection', (view, e) => {
            layoutChannel.request('region:modal').show(new MapSelectionView());
        });

        this.layout.getRegion('mapinfo').show(new MapInfoView({
            model: this.map,
        }));
    },

    select(u) {
        u.set('selected', true);
        this.selectedUsers.add(u);
    },

    unselect(u) {
        u.set('selected', false);
        this.selectedUsers.remove(u);
    },

    selectToggle(id) {
        // cannot add/deselect myself
        if (id === this.authUser.get('id')) return;

        const u = this.lobbyUsers.get(id);
        if (u.get('selected')) {
            this.unselect(u);
        } else {
            this.select(u);
        }
    },
});
