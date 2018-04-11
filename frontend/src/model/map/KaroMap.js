const BaseMap = require('./BaseMap');
module.exports = BaseMap.extend(/** @lends Map.prototype*/{
    defaults: {
        id: 0,
        name: '-',
        author: '-',
        players: 0,
        cps: [],
        rows: 0,
        cols: 0,
        rating: 0
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
        BaseMap.prototype.initialize.apply(this);

        if (!mapId) return;
        this.setId(mapId);
    },

    setId(mapId) {
        if (!mapId) return;

        this.id = mapId;
        this.fetch();
    }
});
