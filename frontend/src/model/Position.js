const Backbone = require('backbone');
const Vector = require('./Vector');
const Position = require('./Position');
module.exports = Backbone.Model.extend(/** @lends Position.prototype */{
    defaults: {
        x: 0,
        y: 0,
    },
    /**
     * @construcor Position
     * @class Position
     * @param x {interger} or {Object} either x or x/y map
     * @param y {integer} optional y
     */
    initialize: function(x, y) {
        // check if first arg is an object with x and y or if we have two numeric args
        if (typeof x === 'object') {
            // we have an object, so we assume default map with x and y
        } else {
            // console.info('Hope for two numbers', x, y, ' Is it?');
            // console.info(typeof x, typeof y);
            if ((typeof x === 'number') && (typeof y === 'number')) {
                this.set('x', x);
                this.set('y', y);
                // console.log(this.toString());
            } else {
                console.error('Vector init messed up: ', x, y);
            }
        }
    },

    toString: function() {
        return '[' + this.get('x') + '|' + this.get('y') + ']';
    },
    move: function(v) {
        this.set('x', this.get('x') + v.get('x'));
        this.set('y', this.get('y') + v.get('y'));
    },

    /**
     * calculates a vector that leads from this pos to given pos
     * @param Position p
     * @return Vector
     */
    getVectorTo: function(p) {
        const vx = p.get('x') - this.get('x');
        const vy = p.get('y') - this.get('y');
        return new Vector({x: vx, y: vy});
    },
    getPassedPositionsTo: function(p) {
        const v = this.getVectorTo(p);
        const vecs = v.getPassedVectors();
        const positions = {};

        for (const vecString in vecs) {
            if (vecs.hasOwnProperty(vecString)) {
                const v = vecs[vecString];
                const pos = new Position(this.attributes);
                pos.move(v);
                positions[pos.toString()] = pos;
            }
        }
        return positions;
    },
});
