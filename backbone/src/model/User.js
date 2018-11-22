const _ = require('underscore');
const Backbone = require('backbone-model-factory');

module.exports = Backbone.ModelFactory(/** @lends User.prototype */ { // eslint-disable-line new-cap
    defaults: {
        id: 0,
        login: 'Gast',
        dran: -1,
        uc: 0,
    },
    /**
     * @class User
     * @constructor User
     */
    initialize() {
        _.bindAll(this, 'increaseDran', 'decreaseDran');
        this.url = '/api/user/' + this.get('id') + '/info.json';
    },
    increaseDran() {
        this.set('dran', this.get('dran') + 1);
    },
    decreaseDran() {
        this.set('dran', this.get('dran') - 1);
    },
});
