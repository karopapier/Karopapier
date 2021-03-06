const Backbone = require('backbone');
const _ = require('underscore');

module.exports = Backbone.View.extend({
    initialize(options) {
        _.bindAll(this, 'render');
        options = options || {};
        if (!options.model) {
            console.error('No model for StatusView');
            return false;
        }
        this.listenTo(this.model, 'change:completed', this.render);
    },
    render() {
        let status = 'Du bist nicht dran';
        if (this.model.get('dranId') === Karopapier.User.get('id')) {
            status = 'Du bist dran, bitte mach Deinen Zug bei ' + this.model.get('id');
        }
        this.$el.html(status);
    },
});
