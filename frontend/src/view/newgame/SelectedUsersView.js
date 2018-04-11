const Backbone = require('backbone');
const LobbyUserView = require('./LobbyUserView');

module.exports = Backbone.View.extend({

    initialize(options) {
        this.map = options.map;
        this.listenTo(this.collection, 'change:selected', this.render);
        this.listenTo(this.collection, 'add remove reset', this.render);
        this.listenTo(this.map, 'change:id', this.render);
    },

    render() {
        const slots = this.map.get('players');

        this.$el.empty();
        let players = 0;
        this.collection.each((model, i) => {
            // mark exceeded @todo raus aus view
            // i is 0-based, so >= not >
            model.set('exceeded', (i >= slots));
            const v = new LobbyUserView({model: model});
            v.render();
            this.$el.append(v.el);
            players++;
        });

        let usedSlots = players;
        while (usedSlots < slots) {
            this.$el.append('<div class="user-slot mod-unused"></div>');
            usedSlots++;
        }
    },
});
