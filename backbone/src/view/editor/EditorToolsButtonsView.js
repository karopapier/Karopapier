const Marionette = require('backbone.marionette');
module.exports = Marionette.ItemView.extend({
    initialize(options) {
        options = options || {};
        if (!options.editorsettings) {
            console.error('No editorsettings passed to EditorToolsButtonView');
            return;
        }
        this.editorsettings = options.editorsettings;
        this.listenTo(this.editorsettings, 'change:buttons', this.update);
    },
    urlFor(f) {
        return '/images/mapfields/' + f + '.png?v=25';
    },
    update(model, buttons) {
        const prev = model.previous('buttons');
        const now = buttons;
        for (let i = 1; i <= 3; i++) {
            if (prev[i] != now[i]) {
                // set new src
                this.$('.button' + i).attr('src', this.urlFor(now[i]));
            }
        }
    },
    render() {
        const buttons = this.editorsettings.get('buttons');
        let html = 'Aktuelle Mausbelegung<br />Links, Mitte, Rechts: ';
        for (let i = 1; i <= 3; i++) {
            html += '<img src="' + this.urlFor(buttons[i]) + '" class="button' + i + '" > ';
        }
        this.$el.html(html);
    },
});

