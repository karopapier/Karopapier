var LocalSyncModel = require('../src/model/LocalSyncModel');

exports.basicTest = function(test) {

    const lsm = new LocalSyncModel({
        context: 'settings',
    });
    lsm.set('data', 'value');
    test.expect(1);
    test.equal(lsm.get('data', 'value', 'set value on model'));
    test.done();
};
