const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

const MapFilterView = require('./MapFilterView');
const MapListView = require('./MapListView');

module.exports = Marionette.View.extend({
    template: require('../../../templates/newgame/mapSelection.html'),
    initialize() {
        this.maps = dataChannel.request('maps');
        this.filter = this.getOption('filter');
    },

    regions: {
        filter: '.map-filter',
        list: '.map-list',
    },

    onRender() {
        this.showChildView('filter', new MapFilterView({
            model: this.filter,
        }));

        this.showChildView('list', new MapListView({
            collection: this.maps,
        }));
    },
});
