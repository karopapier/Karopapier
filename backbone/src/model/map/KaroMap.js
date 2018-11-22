const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    defaults: {
        id: 0,
        name: 'o(-.-)o',
        mapcode: '1',
        loaded: false,
    },
    initialize(...args) {
        // init Maps model
        this.constructor.__super__.initialize.apply(this, args);
    },
    loading() {
        // fill mapcode with growing Xs while waiting

    },
    retrieve() {
        // standard map
        let me = this;
        $.getJSON('/api/map/' + this.get('id') + '.json', (data) => {
            data.loaded = true;
            me.set(data);
        });
    },
});
