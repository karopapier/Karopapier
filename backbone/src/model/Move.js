const Backbone = require('backbone');
const Position = require('./Position');
const Vector = require('./Vector');
const Motion = require('./Motion');

module.exports = Backbone.Model.extend({
    defaults: {
        x: 0,
        y: 0,
        xv: 0,
        yv: 0,
        test: false,
    },
    getMotion() {
        const pos = new Position({x: this.get('x'), y: this.get('y')});
        const vec = new Vector({x: this.get('xv'), y: this.get('yv')});
        return new Motion({
            position: pos,
            vector: vec,
        });
    },
});
