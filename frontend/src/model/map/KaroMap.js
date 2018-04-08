const BaseMap = require('./BaseMap');
module.exports = BaseMap.extend(/** @lends Map.prototype*/{
    defaults: {
        id: 0,
        cps: [],
        rows: 0,
        cols: 0
    },

    url() {
        return '/api/map/' + this.id;
    },

    /**
     * Represents the map and its code
     * @constructor Map
     * @class Map
     */
    initialize(mapId) {
        this.id = mapId;

        BaseMap.prototype.initialize.apply(this);
    }
});
