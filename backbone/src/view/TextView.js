const Backbone = require('backbone');
module.exports = Backbone.View.extend({
    initialize(options) {
        this.text = options.text || '-';
    },
    render() {
        this.$el.html(this.text);
        return this;
    },
});
