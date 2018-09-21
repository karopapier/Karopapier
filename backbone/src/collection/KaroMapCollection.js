const Backbone = require('backbone');
const KaroMap = require('../model/map/KaroMap');

module.exports = Backbone.Collection.extend({
    model: KaroMap,
    url: '/api/map/list.json',
});
