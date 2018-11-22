const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    initialize(options) {
        _.bindAll(this, 'template');
        this.path = options.path;
    },
    template() {
        const path = this.path.replace('.html', '');
        return window.JST['static/' + path];
    },
    render() {
        const content = this.template();
        this.$el.html(content);

        this.$('a[href*=".html"]').click((e) => {
            const href = $(e.currentTarget).attr('href');
            console.log(href);
            Karopapier.router.navigate(href, {trigger: true});
            e.preventDefault();
            return false;
        });
    },
});
