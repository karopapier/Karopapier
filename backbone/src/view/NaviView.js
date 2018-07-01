const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend(/** @lends NaviView.prototype */ {
    template: require('../../templates/main/navi.html'),
    render: function() {
        this.$el.html(this.template());
        this.$('a[href*=".html"]').click(function(e) {
            let href = $(e.currentTarget).attr('href');
            console.log(href);
            Karopapier.router.navigate(href, {trigger: true});
            e.preventDefault();
            return false;
        });
        return this;
    },
});
