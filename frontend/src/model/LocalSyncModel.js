const $ = require('jquery');
const _ = require('underscore');
const Backbone = require('backbone');
const store = require('store');
const eventPlugin = require('store/plugins/events')
store.addPlugin(eventPlugin);

module.exports = Backbone.Model.extend(/** @lends LocalSyncModel */ {
    /**
     * @class LocalSyncModel
     * @constructor LocalSyncModel
     */
    initialize(data, options) {
        // take storageId from data, but remove it again
        this.storageId = this.get('storageId');
        this.unset('storageId');
        if (!this.storageId) {
            this.storageId = 'ID' + Math.round(Math.random() * 10000);
        }

        // console.log("INIT LOCALSYNC ON ", id);
        const cachedData = store.get(this.storageId);
        if (cachedData) {
            // console.log("From store", cachedData);
            this.set(cachedData);
        }
        this.initialized = true;
    },

    set(...args) {
        Backbone.Model.prototype.set.apply(this, args);
        if (this.initialized) {
            this.directSave();
        }
    },

    directSave(e) {
        // console.log("Direct save", e, this.toJSON());
        store.set(this.get('storageId'), this.toJSON());
    },
});
