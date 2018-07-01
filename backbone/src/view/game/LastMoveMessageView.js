const _ = require('underscore');
const Backbone = require('backbone');
const moment = require('moment');
const KaroUtil = require('../../model/Util');

module.exports = Backbone.View.extend({
    initialize: function() {
        this.listenTo(this.collection, 'reset change', this.render);
        _.bindAll(this, 'render');
        this.template = _.template('<small><%= name %> (<%= date %>): &quot;<%= text %>&quot;<br /></small>\n');
        this.settings = new Backbone.Model();
        this.settings.set('timestamp', false);
        this.listenTo(this.settings, 'change:timestamp', this.render);
    },

    render: function() {
        let html = '';
        let ts = this.settings.get('timestamp');

        if ((this.collection.length > 0) && ts) {
            let filter = function(m) {
                let d = moment(m.get('t'), 'YYYY-MM-DD hh:mm:ss');
                return (d.unix() > (ts.getTime() / 1000));
            };

            let filtered = this.collection.filter(filter);
            _.each(filtered, function(e) {
                let tpl = this.template;
                // if (!txt.startsWith("-:K")) {  //do we want to see status messages since last move or not?
                html += tpl({
                    name: e.get('player').get('name'),
                    text: KaroUtil.linkify(e.get('msg')),
                    date: moment(e.get('t'), 'YYYY-MM-DD hh:mm:ss').format('YYYY-MM-DD'),
                });
                // }
            }, this);
        }

        if (!html) {
            this.$el.hide();
            html = '';
        } else {
            html = '<small><b>Bordfunk seit Deinem letzten Zug:</b></small><br>' + html;
            this.$el.show();
        }

        this.$el.html(html);
        // this.$el[0].scrollTop = this.$el[0].scrollHeight;
    },
});
