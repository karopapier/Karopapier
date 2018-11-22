const $ = require('jquery');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    template: require('../../../templates/game/gameTitle.html'),
    initialize() {
        this.listenTo(this.model, 'change:name', this.render);
    },
    render() {
        const $old = this.$el;
        const $new = $(this.template(this.model.toJSON()));
        this.setElement($new);
        $old.replaceWith($new);
        return this;
    },
});
