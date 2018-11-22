const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    initialize(map) {
        if (typeof map === 'undefined') {
            throw new Error('MAP_MISSING');
        }
        // console.log("INit Pathfinder");
        _.bindAll(this, 'reset', 'getMainField', 'getAllOutlines', 'getFieldOutlines', 'getOutlineDirection');
        this.map = map;
        this.reset();

        // console.log("MAP im Finder",this.map);
        this.ROAD_FIELDS = ['O', 'F', 'S', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        this.modifiers = {
            top: {r: -1, c: 0},
            right: {r: 0, c: +1},
            bottom: {r: +1, c: 0},
            left: {r: 0, c: -1},
        };
        this.directions = {
            '-1|0': 'up',
            '0|1': 'right',
            '1|0': 'down',
            '0|-1': 'left',
        };
        this.outlineModifiers = {
            top: {
                from: {r: 0, c: 0},
                to: {r: 0, c: +1},
            },
            right: {
                from: {r: 0, c: +1},
                to: {r: +1, c: +1},
            },
            bottom: {
                from: {r: +1, c: +1},
                to: {r: +1, c: 0},
            },
            left: {
                from: {r: +1, c: 0},
                to: {r: 0, c: 0},
            },
        };
        // console.log("INit Pathfinder DONE");
    },
    reset() {
        this.outlines = {};
    },
    getMainField() {
        function occurrences(string, subString, allowOverlapping) {
            string += '';
            subString += '';
            if (subString.length <= 0) return string.length + 1;

            let n = 0;
            let pos = 0;
            let step = (allowOverlapping) ? (1) : (subString.length);

            while (true) {
                pos = string.indexOf(subString, pos);
                if (pos >= 0) {
                    n++;
                    pos += step;
                } else break;
            }
            return (n);
        }

        let mc = this.map.get('mapcode');
        let mostChar = '';
        let charCount = 0;
        for (let char in this.map.FIELDS) {
            let nb = occurrences(mc, char);
            if (nb) {
                if (nb > charCount) {
                    mostChar = char;
                    charCount = nb;
                }
            }
        }
        return mostChar;
    },
    getAllOutlines() {
        // console.info("Start number crunching");
        this.reset();
        let char;
        let cols = this.map.get('cols');
        let rows = this.map.get('rows');

        let r = 0;
        let c = 0;
        while (r < rows) {
            c = 0;
            while (c < cols) {
                if (this.map.withinBounds({row: r, col: c})) {
                    char = this.map.getFieldAtRowCol(r, c);
                } else {
                    char = '_';
                }

                // if (char == "O") {
                if (char in this.map.FIELDS) {
                    this.getFieldOutlines(r, c);
                }
                c++;
            }
            r++;
        }
        return true;
    },
    getSvgPathFromOutlines(outlines, s) {
        let path = '';
        let emergencyBreak = 10000;
        let lastR = -1;
        let lastC = -1;
        let lastDirection = '';

        // console.info(outlines);

        // get first
        // console.log(outlines);
        const firstOutline = _.first(_.values(outlines));

        // console.debug(firstOutline);

        // set initial "last position" to start of first outline
        lastR = firstOutline[0].y1;
        lastC = firstOutline[0].x1;
        lastDirection = this.getOutlineDirection(firstOutline);
        path = 'M' + lastC * s + ',' + lastR * s;

        // console.log(lastR, lastC);

        while ((!(_.isEmpty(outlines))) && (emergencyBreak > 0)) {
            let searchKey = this.getKeyForRowCol(lastR, lastC);
            // console.log("Looking for key", searchKey);
            // console.log(path);

            if (searchKey in outlines) {
                let a = outlines[searchKey];
                let o = a.shift();
                let thisDirection = this.getOutlineDirection(o);

                if (thisDirection != lastDirection) {
                    path += 'L' + (o.x1 * s) + ',' + (o.y1 * s);
                }

                lastDirection = thisDirection;

                lastC = o.x2;
                lastR = o.y2;

                if (a.length === 0) {
                    // console.log("del ", searchKey);
                    delete outlines[searchKey];
                    // } else {
                    // console.info(o.length);
                }
            } else {
                // console.info("No connection for ", searchKey);
                // console.log("Close");
                path += 'L' + (lastC * s) + ',' + (lastR * s);
                // console.info("Start NEW");
                const firstOutline = _.first(_.values(outlines));
                lastR = firstOutline[0].y1;
                lastC = firstOutline[0].x1;
                lastDirection = this.getOutlineDirection(firstOutline);
                path += 'M' + lastC * s + ',' + lastR * s;
            }

            // console.log(outlines);

            emergencyBreak--;
        }

        // console.log("Break", emergencyBreak);
        path += 'Z';
        return path;
    },

    getOutlineDirection(outline) {
        let x = outline.x2 - outline.x1;
        let y = outline.y2 - outline.y1;
        return this.directions[y + '|' + x];
    },

    getKeyForRowCol(r, c) {
        return (r + '|' + c);
    },
    getRowColFromKey(k) {
        let s = k.split('|');
        return {r: parseInt(s[0]), c: parseInt(s[1])};
    },
    isLikeRoad(s) {
        return this.ROAD_FIELDS.indexOf(s) >= 0;
    },
    getFieldOutlines(r, c) {
        let currentField = this.map.getFieldAtRowCol(r, c);

        // for all 4 directions
        // get the char and if it is different, add outline

        let testField;

        let copyToRoad = false;
        for (let direction in this.modifiers) {
            // console.log("Now doing directions: ",direction,"from ", r,c)
            // console.log("Remembering i am coming from ", from);
            let mod = this.modifiers[direction];
            let testR = r + mod.r;
            let testC = c + mod.c;

            if (this.map.withinBounds({row: testR, col: testC})) {
                testField = this.map.getFieldAtRowCol(testR, testC);
            } else {
                testField = '_';
            }
            // if (this.WILDCARD_FIELDS.indexOf(testField) >= 0) {
            // //no need to draw the outline here
            // return true;
            // }

            // SOme note: if both fields are in the list of ROAD fields, don't consider the outline
            // solution: add an outline of a CP to "not Road" as an outline for road as well
            // if we are on a "not road" and next is a road alternative, copy

            copyToRoad = false;
            if (currentField !== 'O') {
                // now if we are on a "road alternative" and the outline is to something that is not road,
                // add the outline as a road outline as well
                if (this.isLikeRoad(currentField) && !this.isLikeRoad(testField)) {
                    copyToRoad = true;
                }
            }

            if (testField != currentField) {
                // add outlines
                let fromMod = this.outlineModifiers[direction].from;
                let toMod = this.outlineModifiers[direction].to;
                if (!(currentField in this.outlines)) {
                    // init outline per char
                    this.outlines[currentField] = {};
                }

                // use start as key and add array of outlines, as several outlines can start at one point

                let x1 = c + fromMod.c;
                let y1 = r + fromMod.r;
                let x2 = c + toMod.c;
                let y2 = r + toMod.r;
                let k = this.getKeyForRowCol(y1, x1);

                // if we are on a road and next is a road alternative
                if (currentField == 'O' && this.isLikeRoad(testField)) {
                    // don't add it to road outlines
                } else {
                    if (!(k in this.outlines[currentField])) {
                        this.outlines[currentField][k] = [];
                    }
                    this.outlines[currentField][k].push({x1, y1, x2, y2});
                }

                // if we should add to road
                if (copyToRoad) {
                    if (!('O' in this.outlines)) {
                        this.outlines['O'] = {};
                    }
                    if (!(k in this.outlines['O'])) {
                        this.outlines['O'][k] = [];
                    }
                    this.outlines['O'][k].push({x1, y1, x2, y2});
                }
            }
        }

        // top this.outlines[this.getKeyForRowCol(r, c)] = {r: r, c: c + 1};
        // right this.outlines[this.getKeyForRowCol(r, c + 1)] = {r: r + 1, c: c + 1};
        // bottom this.outlines[this.getKeyForRowCol(r + 1, c + 1)] = {r: r + 1, c: c};
        // left this.outlines[this.getKeyForRowCol(r + 1, c)] = {r: r, c: c};
        return true;
    },
})
;
