const _ = require('underscore');
const Marionette = require('backbone.marionette');
const MapListView = require('../map/MapListView');

module.exports = Marionette.ItemView.extend({
    template: window.JST['editor/mapload'],
    initialize(options) {
        if (!options.editorApp) {
            console.error('No editorApp passed to EditorToolsMaploadView');
            return;
        }
        this.editorApp = options.editorApp;
        this.model = options.map;
        _.bindAll(this, 'karoMapChange');
    },
    events: {
        'change .karoMaps': 'karoMapChange',
    },
    karoMapChange(e) {
        const id = this.$('.karoMaps').val();
        const map = this.editorApp.karoMaps.get(id);
        const mapcode = map.get('mapcode');
        this.editorApp.map.setMapcode(mapcode);
    },
    render() {
        this.$el.empty();
        this.$el.html(this.template());
        this.karoMapListView = new MapListView({
            collection: this.editorApp.karoMaps,
            el: this.$('select'),
        });
        this.karoMapListView.render();
        this.editorApp.karoMaps.fetch();
        // this.$el.append(this.karoMapListView.$el);
    },
});
