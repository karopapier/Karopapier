const _ = require('underscore');
const Backbone = require('backbone');
const MoveCollection = require('../collection/MoveCollection');

module.exports = Backbone.Model.extend(/** @lends Player.prototype */{
    /**
     * @construcor Player
     * @class Player
     */
    defaults: {
        id: 0,
        blocktime: -1,
    },
    initialize() {
        _.bindAll(this, 'parse', 'getLastMove');
        if (!this.moves) {
            this.moves = new MoveCollection();
        }
        // this.listenTo(this.moves, "add remove change", this.trigger.bind(this,"movechange"));
        // this.listenTo(this.moves, "reset", this.trigger.bind(this,"movereset"));
    },
    parse(data) {
        if (!this.moves) {
            this.moves = new MoveCollection();
        }
        this.moves.reset(data.moves);
        delete data.moves;
        return data;
    },
    getLastMove() {
        if (this.moves.length > 0) {
            return this.moves.at(this.moves.length - 1);
        } else {
            return false;
        }
    },
    toJSON() {
        const modelJSON = Backbone.Model.prototype.toJSON.call(this);
        modelJSON.moves = this.moves.toJSON();
        return modelJSON;
    },
    getStatus() {
        const means = {
            'kicked': 'rausgeworfen',
            'left': 'ausgestiegen',
            'invited': 'eingeladen',
        };
        const s = this.get('status');
        if (s in means) return means[s];
        return s;
    },
});
