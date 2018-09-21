const $ = require('jquery');
const _ = require('underscore');
const Marionette = require('backbone.marionette');
const PlayerTableRowView = require('./PlayerTableRowView');
const MoveCollection = require('../../collection/MotionCollection');

module.exports = Marionette.CompositeView.extend({
    tagName: 'table',
    className: 'playerCollection playerList thin',
    template: require('../../../templates/game/playerTable.html'),
    childView: PlayerTableRowView,
    childViewContainer: 'tbody',

    initialize: function() {
        _.bindAll(this, 'render');
        this.listenTo(this.collection, 'reset add', this.calcBlocktime);
        // this.listenTo(this.collection, "reset", this.render);
    },

    events: {
        'change input.checkAll': 'checkAll',
    },

    checkAll: function(e) {
        let vis = $(e.currentTarget).prop('checked');
        this.collection.each(function(m) {
            m.set('visible', vis);
        });
    },

    calcBlocktime: function() {
        let moves = new MoveCollection();
        let blocktime = {};
        this.collection.each(function(p) {
            let id = p.get('id');
            blocktime[id] = 0;
            // console.log(p);
            // console.log(p.get("name"), p.moves.length);
            let ms = p.moves.toJSON();
            ms.map(function(m) {
                m.userId = id;
            });
            moves.add(ms);
        });

        moves.comparator = function(m) {
            return new Date(m.get('t').replace(' ', 'T') + 'Z').getTime();
        };
        moves.sort();
        moves.comparator = undefined;
        let lasttime = new Date();
        if (moves.length > 0) {
            lasttime = new Date(moves.at(0).get('t').replace(' ', 'T') + 'Z').getTime();
        }
        moves.each(function(m) {
            let d = new Date(m.get('t').replace(' ', 'T') + 'Z').getTime();
            let userId = m.get('userId');
            blocktime[userId] += (d - lasttime);
            lasttime = d;
        });
        this.collection.each(function(p) {
            p.set('blocktime', parseInt(blocktime[p.get('id')] / 1000));
        });
    },
});
