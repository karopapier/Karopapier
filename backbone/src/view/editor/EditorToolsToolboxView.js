const Marionette = require('backbone.marionette');
module.exports = Marionette.ItemView.extend({
    initialize(options) {
        options = options || {};
        if (!options.editorsettings) {
            console.error('No editorsettings passed to EditorToolsToolboxView');
            return;
        }

        options = options || {};
        if (!options.editorUndo) {
            console.error('No editorUndo passed to EditorToolsToolboxView');
            return;
        }

        this.editorsettings = options.editorsettings;
        this.editorUndo = options.editorUndo;
        this.listenTo(this.editorUndo, 'change:undoStack', this.updateUndoCount);
    },

    events: {
        'click button': 'undo',

    },

    undo() {
        this.editorUndo.undo();
    },

    updateUndoCount(model, buttons) {
        this.undoButton.text('Undo (' + this.editorUndo.undoStack.length + ')');
    },

    render() {
        this.undoButton = $('<button title="Strg+z">Undo</button>');
        this.$el.html(this.undoButton);
        this.updateUndoCount();
    },
});

