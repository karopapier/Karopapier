const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    initialize: function(options) {
        _.bindAll(this, 'template');
        this.path = options.path;
    },
    template: function() {
        let path = this.path.replace('.html', '');
        return window.JST['static/' + path];
    },
    render: function() {
        let content = this.template();
        this.$el.html(content);

        this.$('a[href*=".html"]').click(function(e) {
            let href = $(e.currentTarget).attr('href');
            console.log(href);
            Karopapier.router.navigate(href, {trigger: true});
            e.preventDefault();
            return false;
        });
    },
});
