const _ = require('underscore');
const Backbone = require('backbone');
const ChatMessage = require('../model/ChatMessage');

module.exports = Backbone.Collection.extend({
    url: '/api/chat?limit=100',
    // baseUrl: APIHOST + "/api/chat",
    baseUrl: '/api/chat/list.json',
    model: ChatMessage,
    comparator: 'lineId',
    lastLineId: 0,
    initialize() {
        _.bindAll(this, 'parse', 'cache');
        this.info = new Backbone.Model({
            lastLineId: 0,
        });
    },
    cache(start, limit) {
        if (typeof limit === 'undefined') limit = 100;
        const me = this;
        console.log('Caching', start);
        // TODO check from start to end
        // TODO make sure to grab if start is close to end
        this.fetch({
            url: this.baseUrl + '?start=' + start + '&limit=' + limit + '&callback=?',
            remove: false,
            success() {
                me.trigger('CHAT:CACHE:UPDATED');
            },
        });
    },
    parse(data) {
        // inspect data for max line id
        console.log('parsing cms');
        let ll = this.info.get('lastLineId');
        _.each(data, (cm) => {
            if (cm.lineId > ll) {
                ll = cm.lineId;
            }
        });
        this.info.set('lastLineId', ll);
        return data;
    },
});
