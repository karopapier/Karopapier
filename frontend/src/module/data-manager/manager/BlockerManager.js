const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');
const BlockerCollection = require('../collection/BlockerCollection');

module.exports = Marionette.Object.extend({
    initialize() {
        this.blockers = new BlockerCollection();
        this.blockers.fetch();

        dataChannel.reply('blockers', () => {
            return this.blockers;
        });

        setInterval(() => {
            this.blockers.fetch();
        }, 65000);
    },

    getCollection() {
        return this.blockers;
    },
});
