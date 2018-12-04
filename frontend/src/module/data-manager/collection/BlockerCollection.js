const BaseCollection = require('../../../collection/BaseCollection');
const Blocker = require('../model/Blocker');
module.exports = BaseCollection.extend({
    url: '/api/blockers',
    model: Blocker,
    comparator(m) {
        return -m.get('dran');
    },
});

