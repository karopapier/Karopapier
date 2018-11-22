const _ = require('underscore');
module.exports = Backbone.Model.extend({
    initialize(options) {
        options = options || {};
        if (!options.map) {
            console.error('No map for Conway');
            return false;
        }
        this.map = options.map;
        this.changed = {};
        this.livingNeighbours = {};
        this.currentMap = new Map();
        this.currentNeighbours = {};
        _.bindAll(this, 'step', 'die', 'rise', 'adjustNeighbours', 'countLivingNeighbours', 'isAlive', 'isDead', 'setAllChanged', 'calcField'); // eslint-disable-line max-len
    },
    isAlive(f) {
        return (f === this.livingField());
    },
    isDead(f) {
        return (f === this.deadField());
    },
    deadField() {
        return 'X';
    },
    livingField() {
        return 'O';
    },
    countLivingNeighbours() {
        let cols = this.map.get('cols');
        let rows = this.map.get('rows');
        for (let r = 0, maxR = rows; r < maxR; r++) {
            for (let c = 0, maxC = cols; c < maxC; c++) {
                let livingNeighbours = 0;
                for (let x = -1; x <= 1; x++) {
                    for (let y = -1; y <= 1; y++) {
                        if ((x !== 0) || (y !== 0)) {
                            if (this.map.withinBounds({row: r + y, col: c + x})) {
                                if (this.isAlive(this.map.getFieldAtRowCol(r + y, c + x))) {
                                    livingNeighbours++;
                                }
                            }
                        }
                    }
                }
                this.livingNeighbours[r + '|' + c] = livingNeighbours;
            }
        }
    },
    die(r, c) {
        // console.log("Die",r,c);
        this.map.setFieldAtRowCol(r, c, this.deadField());
        this.adjustNeighbours(r, c, -1);
    },
    rise(r, c) {
        // console.log("Rise",r,c);
        this.map.setFieldAtRowCol(r, c, this.livingField());
        this.adjustNeighbours(r, c, 1);
    },
    adjustNeighbours(r, c, i) {
        for (let x = -1; x <= 1; x++) {
            for (let y = -1; y <= 1; y++) {
                let ry = r + y;
                let cx = c + x;
                let k = ry + '|' + cx;
                if (this.map.withinBounds({row: ry, col: cx})) {
                    if ((x !== 0) || (y !== 0)) {
                        this.livingNeighbours[k] += i;
                    }
                    this.changed[k] = {r: ry, c: cx};
                }
            }
        }
    },
    setAllChanged() {
        let cols = this.map.get('cols');
        let rows = this.map.get('rows');

        for (let r = 0, maxR = rows; r < maxR; r++) {
            for (let c = 0, maxC = cols; c < maxC; c++) {
                this.changed[r + '|' + c] = {r, c};
            }
        }
    },
    calcField(r, c) {
        let field = this.currentMap.getFieldAtRowCol(r, c);
        // console.log("is field", field);
        if ((field === 'X') || (field === 'O') || (field === 'Y') || (field === 'Z')) {
            livingNeighbours = this.currentNeighbours[r + '|' + c];
            // console.log("has living nb", livingNeighbours);

            if (this.isDead(field)) {
                if (livingNeighbours == 3) {
                    this.rise(r, c);
                }
            } else {
                if (livingNeighbours < 2) {
                    this.die(r, c);
                }
                if (livingNeighbours > 3) {
                    this.die(r, c);
                }
            }
        }
    },
    step() {
        this.currentMap.setMapcode(this.map.get('mapcode'));
        this.currentNeighbours = JSON.parse(JSON.stringify(this.livingNeighbours));

        let currentChanged = this.changed;
        this.changed = {};

        for (let k in currentChanged) {
            if (currentChanged.hasOwnProperty(k)) {
                let coords = currentChanged[k];
                r = coords.r;
                c = coords.c;
                // console.log("Calculate", r, c);

                this.calcField(r, c);
            }
        }

        return true;
    },
});
