var Marionette = require('backbone.marionette');

module.exports = Marionette.View.extend({
    template: require('../../../templates/messaging/message.html'),
    templateContext: {
        formatTsIsoDate: function(ts) {
            var dat = new Date(ts * 1000);
            var y = dat.getFullYear();
            var m = dat.getMonth() + 1;
            var d = dat.getDate();
            if (m < 10) m = "0" + m;
            if (d < 10) d = "0" + d;
            return y + "-" + m + "-" + d;
        },
        formatTs: function(ts) {
            var d = new Date(ts * 1000);
            var m = d.getMinutes();
            if (m < 10) m = "0" + m;
            return d.getHours() + ":" + m;
        }
    }
});
