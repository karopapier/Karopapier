const Marionette = require('backbone.marionette');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

module.exports = Marionette.View.extend({
    template: require('../../../templates/messaging/message.html'),
    className: 'message',
    templateContext() {
        return {
            formatTsIsoDate: (ts) => {
                const dat = new Date(ts * 1000);
                const y = dat.getFullYear();
                let m = dat.getMonth() + 1;
                let d = dat.getDate();
                if (m < 10) m = '0' + m;
                if (d < 10) d = '0' + d;
                return y + '-' + m + '-' + d;
            },
            formatTs: (ts) => {
                const d = new Date(ts * 1000);
                let m = d.getMinutes();
                if (m < 10) m = '0' + m;
                return d.getHours() + ':' + m;
            },

            prettyText: () => {
                let text = this.model.get('text');
                const linkifier = dataChannel.request('linkifier');
                text = linkifier.linkify(text);
                text = text.replace(/\n/g, '<br>');
                return text;
            },
        };
    },
});
