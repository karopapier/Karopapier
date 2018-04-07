const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

// Models
const UserFilter = require('../model/newgame/UserFilter');

// Views
const UserFilterView = require('../view/newgame/UserFilterView');
const NewGameLayout = require('../layout/NewGameLayout');

module.exports = Marionette.Application.extend({

    initialize(config) {
        console.log('Init NewGame App');

        this.layout = new NewGameLayout({});

        this.loadInitialAndStart();
    },

    loadInitialAndStart() {
        this.users = dataChannel.request('users');
        this.start();
    },

    start() {
        console.info('Start NewGame App');
        this.userFilter = new UserFilter();
        this.layout.getRegion('userfilter').show(new UserFilterView({
            model: this.userFilter
        }));

        /*
        this.layout.getRegion('userlist').show(new UserlistView({
            collection: this.users,
            filterModel: this.userFilter
        }));
        */
    }
});
