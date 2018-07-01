const _ = require('underscore');
const Marionette = require('backbone.marionette');
const MapListView = require('../map/MapListView');

module.exports = Marionette.ItemView.extend({
    template: window.JST['editor/mapload'],
    initialize: function(options) {
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
    karoMapChange: function(e) {
        let id = this.$('.karoMaps').val();
        let map = this.editorApp.karoMaps.get(id);
        let mapcode = map.get('mapcode');
        this.editorApp.map.setMapcode(mapcode);
    },
    render: function() {
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
