const _ = require('underscore');
const Backbone = require('backbone');
const moment = require('moment');
const KaroUtil = require('../../model/Util');

module.exports = Backbone.View.extend({
    template: _.template('<%= name %> (<%= date %>): &quot;<%= text %>&quot;<br />\n'),
    statusTemplate: _.template('<small><%= name %> (<%= date %>): &quot;<%= text %>&quot;<br /></small>\n'),
    render() {
        // console.log("Rendere Movemessages, derer", this.collection.length);
        let html = '';
        const txt = this.model.get('msg');
        let tpl = this.template;
        if (txt.startsWith('-:K')) {
            tpl = this.statusTemplate;
        }
        html += tpl({
            name: this.model.get('player').get('name'),
            text: KaroUtil.linkify(this.model.get('msg')),
            date: moment(this.model.get('t'), 'YYYY-MM-DD hh:mm:ss').format('YYYY-MM-DD'),
        });

        this.$el.html(html);
    },
});
