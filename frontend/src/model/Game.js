const Backbone = require('backbone');
const Map = require('./map/Map');
module.exports = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: '-',
        dranName: '-',
        blocked: 0,
    },

    url() {
        return '/api/games/' + this.get('id');
    },

    getMap() {
        return new Map(this.map);
    },

});
