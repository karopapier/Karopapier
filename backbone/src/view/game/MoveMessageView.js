var _ = require('underscore');
var Backbone = require('backbone');
var moment = require('moment');
var KaroUtil = require('../../util/TextHelper');

module.exports = Backbone.View.extend({
    template: _.template('<%= name %> (<%= date %>): &quot;<%= text %>&quot;<br />\n'),
    statusTemplate: _.template('<small><%= name %> (<%= date %>): &quot;<%= text %>&quot;<br /></small>\n'),
    render: function() {
        //console.log("Rendere Movemessages, derer", this.collection.length);
        var html = '';
        var txt = this.model.get("msg");
        var tpl = this.template;
        if (txt.startsWith("-:K")) {
            tpl = this.statusTemplate;
        }
        html += tpl({
            name: this.model.get("player"),
            text: KaroUtil.linkify(this.model.get("msg")),
            date: moment(this.model.get("t"), "YYYY-MM-DD hh:mm:ss").format("YYYY-MM-DD")
        });

        this.$el.html(html);
    }
});
