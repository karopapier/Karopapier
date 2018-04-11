const Backbone = require('backbone');

module.exports = Backbone.Collection.extend({
    initialize(...args) {
        this.isLoaded = false;
        this.isLoadedPromise = new Promise((resolve) => {
            this.once('loaded', () => {
                this.isLoaded = true;
                resolve();
            });
        });
        this.on('sync', () => {
            this.loadedTS = new Date();
        });
        this.once('sync', () => {
            this.trigger('loaded');
        });

        Backbone.Collection.prototype.initialize.apply(this, args);
    },

    /**
     * @returns {Promise}
     */
    getLoadedPromise() {
        return this.isLoadedPromise;
    },

    getLoadedTS() {
        return this.loadedTS;
    },
});
