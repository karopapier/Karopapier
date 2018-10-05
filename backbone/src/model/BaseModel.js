const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    initialize(...args) {
        Backbone.Model.prototype.initialize.apply(this, args);

        this.isLoaded = false;
        this.isLoadedPromise = new Promise((resolve, reject) => {
            this.once('loaded', () => {
                this.isLoaded = true;
                resolve();
            });
            this.once('reject', () => {
                this.isLoaded = true;
                reject();
            });
        });
        this.once('sync', () => {
            this.trigger('loaded');
        });
        this.once('error', () => {
            this.trigger('reject');
        });
    },

    /**
     * @returns {Promise}
     */
    getLoadedPromise() {
        return this.isLoadedPromise;
    },
});
