const _ = require('underscore');
const Backbone = require('backbone');
const ChatMessage = require('../model/ChatMessage');

module.exports = Backbone.Collection.extend({
    url: APIHOST + '/api/chat?limit=100&callback=?',
    // baseUrl: APIHOST + "/api/chat",
    baseUrl: APIHOST + '/api/chat/list.json',
    model: ChatMessage,
    comparator: 'lineId',
    lastLineId: 0,
    initialize: function() {
        _.bindAll(this, 'parse', 'cache');
        this.info = new Backbone.Model({
            lastLineId: 0,
        });
    },
    cache: function(start, limit) {
        if (typeof limit === 'undefined') limit = 100;
        let me = this;
        console.log('Caching', start);
        // TODO check from start to end
        // TODO make sure to grab if start is close to end
        this.fetch({
            url: this.baseUrl + '?start=' + start + '&limit=' + limit + '&callback=?',
            remove: false,
            success: function() {
                me.trigger('CHAT:CACHE:UPDATED');
            },
        });
    },
    parse: function(data) {
        // inspect data for max line id
        console.log('parsing cms');
        let ll = this.info.get('lastLineId');
        _.each(data, function(cm) {
            if (cm.lineId > ll) {
                ll = cm.lineId;
            }
        });
        this.info.set('lastLineId', ll);
        return data;
    },
});
