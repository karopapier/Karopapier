const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    defaults: {
        buttons: [null, 'O', '1', 'X'],
        rightclick: true,
        drawmode: 'draw',
    },
    setButtonField(b, f) {
        let buttons = this.get('buttons');
        // unset silently to trigger change event on array
        // https://stackoverflow.com/questions/8491546/models-change-event-wont-fire-when-updating-an-array#
        if (buttons[b] != f) {
            let newButtons = [];
            for (let i = 0; i <= 3; i++) {
                newButtons[i] = buttons[i];
            }
            newButtons[b] = f;
            this.set('buttons', newButtons);
        }
    },
});

