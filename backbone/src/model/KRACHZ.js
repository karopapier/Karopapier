const _ = require('underscore');
const Backbone = require('backbone');
/*
 var crazyHelperFunction = function (mo, depth) {
 var pos1 = mo.getSourcePosition();
 var pos2 = mo.get("position");
 var x1 = parseInt(pos1.get("x") * 12 + 6)
 var x2 = parseInt(pos2.get("x") * 12 + 6)
 var y1 = parseInt(pos1.get("y") * 12 + 6)
 var y2 = parseInt(pos2.get("y") * 12 + 6)
 var f = depth * 16 - 1;

 var l = Karopapier.Util.createSvg("line", {
 x1: x1,
 x2: x2,
 y1: y1,
 y2: y2,
 stroke: "rgb(" + f + ",0,0)" //+f+","+f+")"
 })
 document.getElementById("mapPlayerMoves").appendChild(l);
 };
 */

module.exports = Backbone.Model.extend(/** @lends KRACHZ.prototype*/{
    /**
     * @constructor KRACHZ
     * @class KRACHZ
     *
     * Kalkuliert
     * Regelkonform
     * Alle
     * Crash
     * Herbeiführenden
     * Züge
     *
     * aka "Crash Detection"
     */

    // map
    // motion
    // calc with depth
    initialize(options) {
        _.bindAll(this, 'willCrash');
        if (!options.hasOwnProperty('map') || (typeof options.map === 'undefined')) {
            console.error('No map provided to KRACHZ');
            return false;
        }
        this.cache = {};
    },

    willCrash(mo, depth) {
        // console.warn("starting", mo.toString(), depth);
        const map = this.get('map');
        if (!depth) depth = 8;
        // crazyHelperFunction(mo, depth);
        // TAKES++;

        if (depth === 0) return false;
        if (depth === 1) {
            // console.warn("TIEF 1", mo);
            // crazyHelperFunction(mo, 0);
            return !map.isPossible(mo);
        }
        if (mo.get('vector').toString() == '(0|0)') {
            // console.log("NULLER");
            return false;
        }

        const stop = mo.getStopPosition();
        if (!(map.withinBounds({x: stop.get('x'), y: stop.get('y')}))) {
            this.cache[mo.toString()] = true;
            return true;
        }
        let possibles = mo.getPossiblesByLength();
        possibles = map.verifiedMotions(possibles);
        if (possibles.length == 0) {
            // console.warn("Nothin left");
            return true;
        }

        // I think I could turn around
        if ((mo.get('vector').getLength() == 1) && (possibles.length == 8)) return false;

        let crashes = 0;
        const plen = possibles.length;
        for (let p = 0; p < plen; p++) {
            // console.info(possibles[p],"now")
            const possible = possibles[p];
            const moString = possible.toString();
            // console.log(moString, "Depth:", depth, p + "/" + crashes);
            if (depth >= 1) {
                if (moString in this.cache) {
                    // console.info("Cached",moString);
                    return this.cache[moString];
                }
                const wc = this.willCrash(possible, depth - 1);
                this.cache[moString] = wc;
                if (wc) {
                    crashes++;
                } else {
                    return false;
                }
            }
        }
        // console.info(crashes, possibles.length);
        return crashes == possibles.length;
    },
});
