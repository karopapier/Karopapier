const Backbone = require('backbone');
module.exports = Backbone.View.extend({
    tagName: 'option',
    render() {
        this.$el.attr('value', this.model.get('id'));
        this.$el.html(this.model.get('id') + ' - ' + this.model.get('name'));
    },
});
