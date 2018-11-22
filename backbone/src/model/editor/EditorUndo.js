const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    initialize(options) {
        _.bindAll(this, 'undo');
        options = options || {};
        this.map = options.map;
        if (!this.map) {
            console.error('No map for EditorUndo');
            return false;
        }
        if (!options.editorsettings) {
            console.error('No editorsettings for EditorUndo');
            return false;
        }
        this.editorsettings = options.editorsettings;

        this.undoStack = [];
        this._enabled = true;
        this._lastChangeWasUndo = false;
        this.listenTo(this.editorsettings, 'change:undo', this.checkStatus);
        this.listenTo(this.map, 'change:field', function(e) {
            this.pushChange(e.oldcode);
        });

        this.listenTo(this.map, 'change:mapcode', this.checkChange);
    },

    checkStatus() {
        if (this.editorsettings.get('undo')) {
            this.enable();
        } else {
            this.disable();
        }
    },

    checkChange(e) {
        if (this._lastChangeWasUndo) {
            // console.info("War ein undo");
        } else {
            this.pushChange(this.map.previous('mapcode'));
        }
        this._lastChangeWasUndo = false;
        // console.log("Undo hat noch", this.undoStack.length);
    },

    pushChange(code) {
        if (!this._enabled) return false;
        // console.log("Push ", code, "because", this._enabled);
        const l = this.undoStack.length;
        if (l > 0) {
            const prev = this.undoStack[l - 1];
            // console.log("Compare", prev, code);
            if (code === prev) {
                // console.info("Skip double triggered change, last undo is the same");
                return false;
            }
        }
        this.undoStack.push(code);
        this.trigger('change:undoStack', this.undoStack);
    },

    disable() {
        // console.log("Undo disabled");
        this.pushChange(this.map.get('mapcode'));
        this._enabled = false;
    },

    enable() {
        // console.log("Undo re-enabled, take snapshot")
        this._enabled = true;
        // this.pushChange(this.map.get("mapcode"));
    },


    undo() {
        if (this.undoStack.length >= 1) {
            this._lastChangeWasUndo = true;
            const undocode = this.undoStack.pop();
            // console.log(undocode);
            this.map.set('mapcode', undocode);
            // console.log("Now trigger change:undoStack");
            this.trigger('change:undoStack', this.undoStack);
        }
        // console.log("Undo hat noch", this.undoStack.length);
    },
});
