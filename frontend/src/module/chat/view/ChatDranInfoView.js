const Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    className: 'chat-dran-info',
    template: require('../templates/chat-dran-info.html'),
    initialize() {
        this.listenTo(this.collection, 'add remove reset', this.render);
        console.log(this.collection.length);
    },

    templateContext() {
        const dranCount = this.collection.length;
        let nextId = 0;
        if (dranCount > 0) {
            nextId = this.collection.at(0).get('id');
        }
        return {
            dranCount,
            nextId,
        };
    },
});
