const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    tag: 'div',
    className: 'playerInfo',
    initialize: function() {
        _.bindAll(this, 'render');
    },
    render: function() {
        this.$el.html(this.model.get('id') + '-' + this.model.get('name'));
        this.$el.css({
            position: 'absolute',
            top: 100,
            left: 100,
        });
    },
});
