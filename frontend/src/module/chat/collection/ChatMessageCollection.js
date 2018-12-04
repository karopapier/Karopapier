const ChatMessage = require('../model/ChatMessage');
const BaseCollection = require('../../../collection/BaseCollection');

module.exports = BaseCollection.extend({
    model: ChatMessage,

    parse(data) {
        const parsed = [];

        for (let i = 0; i < data.length; i++) {
            const d = data[i];
            d.id = d.lineId;
            delete(d['lineId']);
            parsed.push(d);
        }

        return parsed;
    },

    comparator: 'id',

    url: '/api/chat/list.json',

    fetchLast() {
        this.fetch({reset: true});
    },

    fetchLatest() {
        this.fetch({data: {limit: 1}, remove: false});
    },

    updateLast() {
        this.fetch({data: {limit: 1}});
    },

    getLast() {
        const l = this.length;
        if (l > 0) {
            return this.at(this.length - 1);
        }

        return {};
    },

    getLastId() {
        const l = this.getLast();
        if (l) {
            return l.get('id');
        }

        return 0;
    },
});
