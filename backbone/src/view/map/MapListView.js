const Marionette = require('backbone.marionette');
const MapListItemView = require('./MapListItemView');

module.exports = Marionette.CollectionView.extend({
    tagName: 'select',
    childView: MapListItemView,
    childViewContainer: 'select',
    template: window.JST['map/listView'],
    events: {
        'change select': 'selected',
    },
    selected(e) {
        let $select = $(e.currentTarget);
        let id = $select.val();
        let m = this.collection.get(id);
        this.trigger('selected', m);
    },
});
