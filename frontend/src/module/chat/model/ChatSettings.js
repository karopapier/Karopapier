const LocalSyncModel = require('../../../model/LocalSyncModel');

module.exports = LocalSyncModel.extend({
    defaults: {
        history: false,
        funny: true,
        limit: 20,
        lastLineId: 0,
        showBotrix: false,
        oldLink: false,
        follow: true,
        storageId: 'settings',
    },
    initialize(...args) {
        LocalSyncModel.prototype.initialize.apply(this, args);
    },
});
