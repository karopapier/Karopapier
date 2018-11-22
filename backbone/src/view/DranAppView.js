const Marionette = require('backbone.marionette');

module.exports = Marionette.ItemView.extend({
    template: false, // 2.4.7
    className: 'dranAppView',
    render() {
        this.model.layout.render();
        // insert views
        this.model.layout.dranGames.show(this.model.gamesView);
        this.$el.html(this.model.layout.$el);
    },
});
