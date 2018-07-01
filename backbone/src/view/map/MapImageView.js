const _ = require('underscore');

module.exports = MapBaseView.extend({
    className: 'mapImageView',
    tag: 'img',
    initialize: function(...args) {
        // init MapBaseView with creation of a settings model
        this.constructor.__super__.initialize.apply(this, args);
        _.bindAll(this, 'render');
        this.listenTo(this.model, 'change:id', this.render);
        this.listenTo(this.settings, 'change', this.render);
    },
    render: function() {
        let mapid = this.model.get('id');
        if (mapid === 0) {
            this.$el.hide();
        } else {
            // console.info("Getting image");
            let cps = (this.settings.get('cpsActive') === true) ? 1 : 0;
            this.$el.show();
            this.$el.attr('src', '/images/loading.gif');
            this.$el.attr('src', '/map/' + mapid + '.png?size=' + this.settings.get('size') + '&border=' + this.settings.get('border') + '&cps=' + cps); // eslint-disable-line max-len
        }
    },
});
