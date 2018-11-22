const _ = require('underscore');
const Marionette = require('backbone.marionette');

module.exports = Marionette.ItemView.extend({
    template: window.JST['editor/viewsettings'],
    initialize(options) {
        options = options || {};
        if (!options.viewsettings) {
            console.error('No viewsettings passed to EditorToolsSettingsView');
            return;
        }
        this.viewsettings = options.viewsettings;
        this.listenTo(this.viewsettings, 'change:size change:border', this.update);
        this.listenTo(this.viewsettings, 'change:specles', this.update);
        _.bindAll(this, 'changeSizeBorder', 'changeSpecles');
    },
    events: {
        'input input[name=\'size\']': 'changeSizeBorder',
        'input input[name=\'border\']': 'changeSizeBorder',
        'change input[name=\'specles\']': 'changeSpecles',
    },
    changeSizeBorder(e) {
        let size = parseInt(this.$('.editor-tools-settings-size').val());
        let border = parseInt(this.$('.editor-tools-settings-border').val());
        if (size < 1) size = 1;
        if (size > 50) size = 50;
        if (border > 20) border = 20;
        if (border < 0) border = 0;
        this.viewsettings.set({
            size,
            border,
        });
    },
    changeSpecles(e) {
        let checked = this.$('.editor-tools-settings-specles').prop('checked');
        this.viewsettings.set('specles', checked);
    },

    update(e) {
        this.$('.editor-tools-settings-size').val(this.viewsettings.get('size'));
        this.$('.editor-tools-settings-border').val(this.viewsettings.get('border'));
        this.$('.editor-tools-settings-specles').prop('checked', this.viewsettings.get('specles'));
    },
    render() {
        this.$el.html(this.template());
    },
});
