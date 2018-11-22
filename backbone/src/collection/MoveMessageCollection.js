const _ = require('underscore');
const Backbone = require('backbone');
const Move = require('../model/Move');

module.exports = Backbone.Collection.extend(/** @lends MoveMessageCollection.prototype */{
    /**
     * @constructor MoveMessageCollection
     * @class MoveMessageCollection
     */
    model: Move,
    initialize() {
        _.bindAll(this, 'updateFromPlayers');
    },

    comparator(mm) {
        return mm.get('t');
    },

    updateFromPlayers(players) {
        let msgs = [];
        players.each((p) => {
            const withMessage = p.moves.filter((m) => {
                return m.get('msg');
            });
            _.each(withMessage, (m) => {
                m.set('player', p);
            });
            msgs = msgs.concat(withMessage);
        });
        this.reset(msgs);
    },
});
