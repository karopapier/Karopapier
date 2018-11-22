const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.Model.extend(/** @lends NotificationControl.prototype */ {
    defaults: {
        supported: undefined,
        granted: false,
        denied: false,
        final: false,
        enabled: false,
    },
    /**
     * @constructor NotificationControl
     * @class NotificationControl
     */
    initialize() {
        _.bindAll(this, 'granted', 'unsupported', 'denied', 'finaldenied', 'check', 'request');
        // console.log("INIT WEB NOT");
        this.listenTo(this, 'change', this.status);
        this.listenTo(this, 'change:enabled', this.request);
        this.check();
    },
    unsupported() {
        // console.log("Browser kann nicht");
        this.set({
            'supported': false,
            'final': true,
            'enabled': false,
        });
    },
    finaldenied() {
        this.set({
            granted: false,
            denied: true,
            final: true,
            enabled: false,
        });
    },
    granted() {
        this.set({granted: true, denied: false, final: true});
    },
    denied() {
        this.set({granted: false, denied: true, final: true, enabled: false});
    },
    request() {
        if (this.get('enabled')) {
            const me = this;
            Notification.requestPermission((result) => {
                // console.log(result);
                if (result === 'denied') {
                    me.denied();
                    return;
                } else if (result === 'default') {
                    me.set({granted: false, denied: false, enabled: false, final: false});
                    return;
                }
                me.granted();
            });
        }
    },
    status() {
        return true;
        console.log('-------------------------------');
        for (const k in this.attributes) {
            console.log(k, this.attributes[k]);
        }
    },
    check() {
        if (!('Notification' in window)) {
            this.unsupported();
        } else {
            this.set('supported', true);
            if (Notification.permission === 'denied') {
                this.finaldenied();
            } else {
                this.request();
            }
        }
    },
});
