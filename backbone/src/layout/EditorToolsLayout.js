const Marionette = require('backbone.marionette');
const EditorToolsFieldsView = require('../view/editor/EditorToolsFieldsView');
const EditorToolsToolboxView = require('../view/editor/EditorToolsToolboxView');
const EditorToolsButtonsView = require('../view/editor/EditorToolsButtonsView');
const EditorToolsSettingsView = require('../view/editor/EditorToolsSettingsView');
const EditorToolsMaploadView = require('../view/editor/EditorToolsMaploadView');

module.exports = Marionette.View.extend({
    template: require('../../templates/editor/tools.html'),
    initialize(options) {
        options = options || {};
        if (!options.editorApp) {
            console.error('No editorApp passed to EditorToolsView');
            return;
        }
        this.editorApp = options.editorApp;
        this.viewsettings = this.editorApp.viewsettings;
        this.editorsettings = this.editorApp.editorsettings;
    },
    regions: {
        'fields': '.editor-tools-fields',
        'buttons': '.editor-tools-mousebuttons',
        'toolbox': '.editor-tools-toolbox',
        'settings': '.editor-tools-viewsettings',
        'mapload': '.editor-tools-mapload',
    },
    onShow() {
        this.fields.show(new EditorToolsFieldsView({
            editorsettings: this.editorsettings,
        }));
        this.toolbox.show(new EditorToolsToolboxView({
            editorsettings: this.editorApp.editorsettings,
            editorUndo: this.editorApp.editorUndo,
        }));
        this.buttons.show(new EditorToolsButtonsView({
            editorsettings: this.editorsettings,
        }));
        this.settings.show(new EditorToolsSettingsView({
            viewsettings: this.viewsettings,
        }));
        this.mapload.show(new EditorToolsMaploadView({
            editorApp: this.editorApp,
        }));
    },
});

