const $ = require('jquery');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    template: require('../../../templates/game/gameTitle.html'),
    initialize: function() {
        this.listenTo(this.model, 'change:name', this.render);
    },
    render: function() {
        let $old = this.$el;
        let $new = $(this.template(this.model.toJSON()));
        this.setElement($new);
        $old.replaceWith($new);
        return this;
    },
});
