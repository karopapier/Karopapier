const Backbone = require('backbone');
const Map = require('./map/Map');
module.exports = Backbone.Model.extend({

    url: function() {
        return '/api/games/' + this.get('id');
    },

    getMap() {
        return new Map(this.map);
    }

});
