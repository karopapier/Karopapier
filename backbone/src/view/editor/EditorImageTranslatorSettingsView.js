const _ = require('underscore');
const Marionette = require('backbone.marionette');

module.exports = Marionette.ItemView.extend({
    template: window.JST['editor/imagetranslatorsettings'],
    initialize(options) {
        options = options || {};
        if (!options.imageTranslator) {
            console.error('No imageTranslator passed to TranslatorSettingsView');
            return;
        }
        this.imageTranslator = options.imageTranslator;
        _.bindAll(this, 'changeScale', 'changeSetting', 'update', 'run');
        this.listenTo(this.imageTranslator.settings, 'change:active', this.render);
        this.listenTo(this.imageTranslator.settings, 'change:binary', this.render);
        this.listenTo(this.imageTranslator.settings, 'change', this.update);
        this.listenTo(this.imageTranslator.editorsettings, 'change:buttons', this.render);
    },
    events: {
        'click button': 'run',
        'input input[name=\'scaleWidth\']': 'changeScale',
        'click input[name=\'invert\']': 'changeSetting',
        'click input[name=\'speedmode\']': 'changeSetting',
        'click input[name=\'binary\']': 'changeSetting',
        // "input input[name='scaleHeight']": "changeScale"
    },

    update() {
        // console.log("EITSV update");
        this.$('.editor-imagetranslator-settings-invert').prop('checked', this.imageTranslator.settings.get('invert'));
        this.$('.editor-imagetranslator-settings-speedmode').prop('checked', this.imageTranslator.settings.get('speedmode')); // eslint-disable-line max-len
        this.$('.editor-imagetranslator-settings-binary').prop('checked', this.imageTranslator.settings.get('binary'));
        this.$('.editor-imagetranslator-settings-scaleWidth').val(this.imageTranslator.settings.get('scaleWidth'));
    },

    changeSetting() {
        const binary = this.$('.editor-imagetranslator-settings-binary').prop('checked');
        this.imageTranslator.settings.set('binary', binary);

        const invert = this.$('.editor-imagetranslator-settings-invert').prop('checked');
        this.imageTranslator.settings.set('invert', invert);

        const speedmode = this.$('.editor-imagetranslator-settings-speedmode').prop('checked');
        this.imageTranslator.settings.set('speedmode', speedmode);

        console.log('Now bin invert, speed', binary, invert, speedmode);
    },

    changeScale() {
        let scW = parseInt(this.$('.editor-imagetranslator-settings-scaleWidth').val());
        // console.log("SC", scW);
        if (scW < 1) scW = 1;
        this.imageTranslator.settings.setScale(scW);
    },

    run() {
        this.imageTranslator.run();
    },

    render() {
        // console.log("EITSV render");
        const json = this.imageTranslator.settings.toJSON();
        _.defaults(json, this.imageTranslator.editorsettings.toJSON());
        // console.log(json);
        this.$el.html(this.template(json));
        this.update();
    },
});
