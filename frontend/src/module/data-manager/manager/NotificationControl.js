const Backbone = require('backbone');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

module.exports = Backbone.Model.extend({
    defaults: {
        permission: 'unknown',
    },
    initialize() {
        this.check();
        dataChannel.reply('notificationControl', this);
    },

    check() {
        if (!('Notification' in window)) {
            this.set('permission', 'unsupported');
            return;
        }

        this.set('permission', Notification.permission);
    },

    request() {
        if (this.get('permission') == 'granted') {
            return;
        }

        if (this.get('permission') === 'unsupported') {
            alert('Sorry, aber Dein Browser macht da einfach nicht mit. Schon mal über was aktuelles nachgedacht?');
            return;
        }

        if (this.get('permission') === 'denied') {
            alert('Du hast das Deinem Browser verboten - DU musst das irgendwie wieder einschalten');
            return;
        }

        Notification.requestPermission().then((permission) => {
            this.set('permission', permission);

            const n = new Notification('TOLL!!!', {body: 'Jetzt geht\'s!'});
            setTimeout(() => {
                n.close();
            }, 5000);
        });
    },
});
