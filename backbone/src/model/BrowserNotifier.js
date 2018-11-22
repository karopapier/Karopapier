const Backbone = require('backbone');
const BrowserNotification = require('./BrowserNotification');

module.exports = Backbone.Model.extend(/** @lends BrowserNotifier.prototype*/{
    defaults: {},
    /**
     * @constructor BrowserNotifier
     * @class BrowserNotifier
     * BrowserNotifier manages all notifications to be shown in the browser
     *
     */
    initialize(options) {
        this.eventEmitter = options.eventEmitter;
        this.user = options.user;
        this.settings = options.settings;
        this.control = options.control;

        this.eventEmitter.on('CHAT:MESSAGE', (data) => {
            // console.warn(data.chatmsg);
            new BrowserNotification({
                title: data.user + ' spricht',
                body: data.text,
                level: 'info',
                group: 'global',
                tag: 'chat',
                icon: '/favicon.ico',
                timeout: 10000,
                onClick() {
                    window.open('/index.html');
                },
            });
        });

        this.listenTo(this.user, 'change:dran', this.updateDran);
    },
    updateDran(data) {
        const dran = this.user.get('dran');
        let title = 'Du bist ein bisschen dran (' + dran + ')';
        if (dran == 0) title = 'Du bist gar nich dran!';
        if (dran > 10) title = 'Du bist ganz schön dran! (' + dran + ')';
        if (dran > 20) title = 'Du bist mal echt voll dran! (' + dran + ')';
        if (dran > 30) title = 'BOAH!! Du bist sooo dran! (' + dran + ')';
        if (dran > 40) title = 'LOS! Du bist verdammt dran! (' + dran + ')';
        let en = '';
        if (dran != 1) en = 'en';
        const text = 'Du bist bei ' + dran + ' Spiel' + en + ' dran';
        new BrowserNotification({
            title,
            tag: 'dran',
            body: text,
            icon: '/favicon.ico',
            timeout: dran > 0 ? 0 : 2000,
            // permissionDenied: permissionDenied,
            onClick() {
                // window.open("http://www.karopapier.de/showmap.php?GID="+data.gid);
                window.open('/dran.html');
            },
        });
    },
});
