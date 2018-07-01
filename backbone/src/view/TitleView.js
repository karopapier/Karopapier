const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.View.extend(/** @lends TitleView */{
    initialize: function(options) {
        _.bindAll(this, 'render');

        this.title = options.title || 'Karopapier';

        // expects User as model
        this.model.on('change:dran', this.render);
    },
    render: function() {
        let t = '';
        let dran = this.model.get('dran');

        if (dran > 0) {
            t += '(' + dran + ') ';
        }
        t += this.title;
        document.title = t;
    },
});
