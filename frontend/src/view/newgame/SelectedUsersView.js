const Marionette = require('backbone.marionette');
const EmptySlotView = Marionette.View.extend({
    className: 'user-slot mod-unused',
    template() {
        return '';
    },
});

module.exports = Marionette.CollectionView.extend({
    childView: require('./LobbyUserView'),

    initialize() {
        this.map = this.getOption('map');
        this.listenTo(this.collection, 'change:selected', this.render);
        this.listenTo(this.map, 'change:id', this.render);
    },

    filter(model, index, collection) {
        return model.get('selected');
    },

    onRender() {
        const players = this.collection.length;
        const slots = this.map.get('players');

        if (slots > players) {
            for (let i = 0, l = (slots - players); i < l; i++) {
                this.addChildView(new EmptySlotView());
            }
        }
    },
});
