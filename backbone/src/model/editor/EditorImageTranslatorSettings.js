const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    defaults: {
        active: false,
        binary: true, // X or O
        speedmode: true,
        inverted: false,
        scaleWidth: 10,
        scaleHeight: 10,
        targetRows: 20,
        targetCols: 30,
        sourceWidth: 300,
        sourceHeight: 200,
        fieldtime: 0,
    },

    initialize() {
        // _.bindAll(this, "recalcFromTarget", "recalcFromScale");
        // bindings
        this.listenTo(this, 'change:sourceWidth change:sourceHeight', this.recalcFromSource);
    },

    setScale(sc) {
        if (sc == 0) return false;
        if (sc < 1) return false;
        let tc = Math.floor(this.get('sourceWidth') / sc);
        let tr = Math.floor(this.get('sourceHeight') / sc);
        this.set({
            scaleWidth: sc,
            scaleHeight: sc,
            targetRows: tr,
            targetCols: tc,
        });
    },

    setTargetRowCol(r, c) {
        let scw = this.get('sourceWidth') / c;
        let sch = Math.floor(this.get('sourceHeight') / r);
        this.set({
            scaleWidth: scw,
            scaleHeight: sch,
            targetRows: r,
            targetCols: c,
        });
    },

    recalcFromSource() {
        // console.log("Calc from source");
        // assume default target cols of 60
        // calc scale to match that

        let srcW = this.get('sourceWidth');
        let srcH = this.get('sourceHeight');
        let sc = 10;
        if ((srcW < 60) || (srcH < 40)) {
            sc = 1;
        } else {
            sc = Math.floor(this.get('sourceWidth') / 60);
        }

        // console.log("Set scale and target");
        this.set({
            scaleWidth: sc,
            scaleHeight: sc,
            targetCols: Math.floor(srcW / sc),
            targetRows: Math.floor(srcH / sc),
        });
        // console.log("Scale and target set");
    },
});
