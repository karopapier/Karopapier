const $ = require('jquery');
const _ = require('underscore');
const Backbone = require('backbone');
const store = require('store');

module.exports = Backbone.Model.extend(/** @lends LocalSyncModel */ {
    defaults: {
        storageId: 'ID' + Math.round(Math.random() * 10000),
    },
    /**
     * @class LocalSyncModel
     * @constructor LocalSyncModel
     */
    initialize() {
        _.bindAll(this, 'directSave', 'onStorageEvent');
        $(window).bind('storage', this.onStorageEvent);
        const id = this.get('storageId');
        // console.log("INIT LOCALSYNC ON ", id);
        const data = store.get(id);
        // console.log("From store",data);
        // data = JSON.parse(data);
        // console.log("Data now", data);
        // console.log(this.attributes);
        this.set(data);
        this.initialized = true;
    },
    set(...args) {
        Backbone.Model.prototype.set.apply(this, args);
        if (this.initialized) this.directSave();
    },
    onStorageEvent(e) {
        const key = e.originalEvent.key;
        const val = e.originalEvent.newValue;
        if (key === this.get('storageId')) {
            // console.log("ICH SOLL WERDEN", val);
            const j = JSON.parse(val);
            // console.log(j);
            this.set(j);
        }
    },
    directSave(e) {
        // console.log("Direct save", e, this.toJSON());
        store.set(this.get('storageId'), this.toJSON());
    },
});
