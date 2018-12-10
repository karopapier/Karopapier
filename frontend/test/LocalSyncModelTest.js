var LocalSyncModel = require('../src/model/LocalSyncModel');
const store = require('store');

exports.basicTest = function(test) {

    const lsm = new LocalSyncModel({
        storageId: 'settings',
    });
    lsm.set('data', 'value');
    test.expect(1);
    test.equal(lsm.get('data'), 'value', 'set value on model');
    test.done();
};

exports.cacheTest = function(test) {

    store.set('sny', {'brubbu': 'blabber'});
    const lsm = new LocalSyncModel({
        storageId: 'sny',
    });
    test.expect(1);
    test.equal(lsm.get('brubbu'), 'blabber', 'get value from cache');
    test.done();
};
