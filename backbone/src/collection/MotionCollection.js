const Backbone = require('backbone');
const Motion = require('../model/Motion');

module.exports = Backbone.Collection.extend(/** @lends MotionCollection.prototype */ {
    model: Motion,
    getByMotionString(moString) {
        let motion = false;
        this.each((mo) => {
            if (mo.toString() === moString) {
                motion = mo;
            }
        });
        return motion;
    },
});
