const Backbone = require('backbone');
const Position = require('../Position');
const TextHelper = require('../../util/TextHelper');
module.exports = Backbone.Model.extend(/** @lends Map.prototype*/{
    defaults: {
        id: 0,
        cps: [],
        rows: 0,
        cols: 0,
    },
    /**
     * Represents the map and its code
     * @constructor Map
     * @class Map
     */
    initialize: function() {
        this.validFields = Object.keys(this.FIELDS);
        this.offroadRegEx = new RegExp('(X|P|L|G|N|V|T|W|Y|Z|_)');

        // sanitization binding
        this.bind('change:mapcode', this.updateMapcode);
    },
    FIELDS: {
        'F': 'finish',
        'O': 'road',
        'P': 'parc',
        'S': 'start',
        'G': 'gold',
        'L': 'lava',
        'N': 'snow',
        'T': 'tar',
        'V': 'mountain',
        'W': 'water',
        'X': 'grass',
        'Y': 'sand',
        'Z': 'mud',
        '.': 'night',
        '1': 'cp1',
        '2': 'cp2',
        '3': 'cp3',
        '4': 'cp4',
        '5': 'cp5',
        '6': 'cp6',
        '7': 'cp7',
        '8': 'cp8',
        '9': 'cp9',
    },
    isValidField: function(c) {
        return this.validFields.indexOf(c.toUpperCase()) >= 0;
    },
    setMapcode: function(mapcode) {

        // make sure we don't have CR in there and make it all UPPERCASE
        if (typeof  mapcode === 'undefined') {
            console.error('No mapcode in setMapcode', mapcode);
            return;
        }
        if (typeof mapcode !== 'string') {
            console.error('Mapcode kein String', mapcode);
            return
        }

        let trimcode = mapcode.toUpperCase();
        trimcode = trimcode.replace(/\r/g, '');

        // nb of start positions ('S')
        const starties = (trimcode.match(/S/g) || []).length;

        // calc rows and cols
        const lines = trimcode.split('\n');
        const rows = lines.length;
        const line = lines[0].trim();
        const cols = line.length;
        const cps = this.getCpList(trimcode);

        this.set({
            'mapcode': trimcode,
            'starties': starties,
            'rows': rows,
            'cols': cols,
            'cps': cps,
        });
    },

    getMapcodeAsArray: function() {
        return this.get('mapcode').split('\n');
    },

    setMapcodeFromArray: function(a) {
        this.setMapcode(a.join('\n'));
    },

    floodfill: function(row, col, color) {
        const oldColor = this.getFieldAtRowCol(row, col);
        this.fillstack = [];
        // console.log('Start fill', row, col, color);
        if (oldColor === color) return false;
        this.floodFill4(row, col, oldColor, color);
    },

    floodFill4: function(row, col, oldColor, color) {
        this.fillstack.push({row: row, col: col});
        while (this.fillstack.length > 0) {
            const rc = this.fillstack.pop();
            const r = rc.row;
            const c = rc.col;
            if (this.withinBounds({row: r, col: c})) {
                const field = this.getFieldAtRowCol(r, c);
                if (field === oldColor) {
                    this.setFieldAtRowCol(r, c, color);

                    this.fillstack.push({row: r, col: c + 1});
                    this.fillstack.push({row: r, col: c - 1});
                    this.fillstack.push({row: r + 1, col: c});
                    this.fillstack.push({row: r - 1, col: c});
                }
            }
        }
    },

    addRow: function(count, index) {
        /**
         * @param counter number of rows to insert
         * @param index   'before where to add'. 0 is at front; undefined or negative at end
         */

        const codeRows = this.getMapcodeAsArray();
        const l = codeRows.length;
        if (l == 0) return false;
        if (count == 0) return false;
        let src = '';

        // normalize undefined index to negative
        if (typeof index === 'undefined') index = -1;

        // find row to add
        if (index === 0) {
            src = codeRows[0];
        } else {
            src = codeRows[l - 1];
        }

        // modifying operation
        let op = function() {
        };
        if (index === 0) {
            op = Array.prototype.unshift;
        } else {
            op = Array.prototype.push;
        }

        for (let i = 1; i <= count; i++) {
            op.call(codeRows, src);
        }

        this.setMapcodeFromArray(codeRows);
    },
    addCol: function(count, index) {
        /**
         * @param counter number of cols to insert
         * @param index   "before where to add". 0 is at front; undefined or negative at end
         */

        const codeRows = this.getMapcodeAsArray();
        const l = codeRows.length;
        if (l == 0) return false;
        if (count == 0) return false;

        // normalize undefined index to negative
        if (typeof index === 'undefined') index = -1;

        let f;
        if (index === 0) {
            f = function(row) {
                const first = row[0];
                const pad = TextHelper.repeat(first, count);
                return pad + row;
            };
        } else {
            f = function(row) {
                const last = row.slice(-1);
                const pad = TextHelper.repeat(last, count);
                return row + pad;
            };
        }

        const newCodeRows = codeRows.map(f, count);
        this.setMapcodeFromArray(newCodeRows);
    },

    delRow: function(count, index) {
        /**
         * @param counter number of cols to delete
         * @param index   'before where to delete'. 0 is at front; undefined or negative at end
         */

        const codeRows = this.getMapcodeAsArray();
        const l = codeRows.length;
        if (l == 0) return false;
        if (count == 0) return false;
        if (count > l) return false;

        // calc slice params
        // they define 'what remains'
        let sliceStart = 0;
        let sliceEnd = l;
        if (index == 0) {
            sliceStart = count;
            sliceEnd = l;
        } else {
            sliceStart = 0;
            sliceEnd = -count;
        }

        const newCodeRows = codeRows.slice(sliceStart, sliceEnd);
        this.setMapcodeFromArray(newCodeRows);
    },

    delCol: function(count, index) {
        /**
         * @param counter number of cols to delete
         * @param index   'before where to delete'. 0 is at front; undefined or negative at end
         */

        const codeRows = this.getMapcodeAsArray();
        const l = codeRows.length;
        if (l < 1) return false;
        const cols = codeRows[0].length;
        if (cols == 0) return false;
        if (count == 0) return false;
        if (count > cols) return false;

        // calc slice params
        // they define 'what remains'
        let sliceStart = 0;
        let sliceEnd = 0;
        if (index == 0) {
            sliceStart = count;
            sliceEnd = cols;
        } else {
            sliceStart = 0;
            sliceEnd = -count;
        }

        // define function that is apply to every row
        let f = function(row) {
            return row.slice(sliceStart, sliceEnd);
        };

        const newCodeRows = codeRows.map(f, count);
        this.setMapcodeFromArray(newCodeRows);
    },

    updateMapcode: function(e, mapcode) {
        this.setMapcode(mapcode);
    },
    sanitize: function() {
        // console.log('sanitize and set correct code');

        const dirtyCode = TextHelper.trim(this.get('mapcode').toUpperCase());
        const starties = (dirtyCode.match(/S/g) || []).length;

        // find longest line
        const rows = dirtyCode.split('\n');
        let rowlength = 0;
        rows.forEach(function(row) {
            if (row.length > rowlength) {
                rowlength = row.length;
            }
        });

        // pad lines to match longest and replace invalid Characters
        const cleanRows = [];
        let parcs = 0;
        const me = this;
        rows.forEach(function(row) {
            if (row.length < rowlength) {
                row += Array(rowlength - row.length + 1).join('X');
            }

            let cleanRow = '';

            for (let i = 0; i < rowlength; i++) {
                const c = row[i];
                if (me.isValidField(c)) {
                    cleanRow += row[i];
                } else {
                    cleanRow += 'X';
                }
            }

            // set as many parcs as we have starties
            if (parcs < starties) {
                cleanRow = 'P' + cleanRow.substr(1);
                parcs++;
            }
            cleanRows.push(cleanRow);
        });

        cleanCode = cleanRows.join('\n');
        // console.info(cleanCode);
        this.set('mapcode', cleanCode);

        // Make sure to remove \n at last line
    },
    getStartPositions: function() {
        return this.getFieldPositions('S');
    },
    getCpPositions: function(mapcode) {
        return this.getFieldPositions('\\d', mapcode);
    },
    getFieldPositions: function(field, mapcode) {
        let positions = [];
        let re = new RegExp(field, 'g');
        mapcode = mapcode || this.get('mapcode');
        let hit;
        while (hit = re.exec(mapcode)) {
            const pos = hit.index;
            positions.push(new Position(this.getRowColFromPos(pos)));
        }
        return positions;
    },
    getCpList: function(mapcode) {
        mapcode = mapcode || this.get('mapcode');
        return (mapcode.match(/\d/g) || []).sort().filter(function(el, i, a) {
            if (i == a.indexOf(el)) return 1;
            return 0;
        });
    },
    withinBounds: function(opt) {
        let x;
        let y;
        if ((opt.hasOwnProperty('row')) && opt.hasOwnProperty('col')) {
            x = opt.col;
            y = opt.row;
        } else if ((opt.hasOwnProperty('x')) && (opt.hasOwnProperty('y'))) {
            x = opt.x;
            y = opt.y;
        } else {
            console.error(opt);
            throw new Error('param for withinBounds unclear');
        }
        if (x < 0) return false;
        if (y < 0) return false;
        if (x > this.get('cols') - 1) return false;
        if (y > this.get('rows') - 1) return false;
        return true;
    },
    setFieldAtRowCol: function(r, c, field) {
        const pos = this.getPosFromRowCol(r, c);
        const oldcode = this.get('mapcode');
        // console.log('Mapcodecheck');
        // only if different
        const oldfield = oldcode[pos];
        if (oldfield !== field) {
            mapcode = oldcode.substr(0, pos) + field + oldcode.substr(pos + 1);
            this.set('mapcode', mapcode, {silent: true});
            // trigger field change instead
            this.trigger('change:field', {r: r, c: c, field: field, oldfield: oldfield, oldcode: oldcode});
            // console.log('Change triggered');
        }
    },
    /**
     *
     * @param r 0..rows-1
     * @param c 0..cols-1
     * @returns {String}
     */
    getFieldAtRowCol: function(r, c) {
        // console.log(r, c);
        if (!this.withinBounds({row: r, col: c})) {
            console.error(r, c);
            throw new Error('Row ' + r + ', Col ' + c + ' not within bounds');
        }
        const pos = this.getPosFromRowCol(r, c);
        // console.log('Ich sag',pos);
        return this.get('mapcode').charAt(pos);
    },

    getPosFromRowCol: function(r, c) {
        return (r * (this.get('cols') + 1)) + c;
    },

    getRowColFromPos: function(pos) {
        const cols = this.get('cols') + 1;
        const c = pos % cols;
        const r = Math.floor(pos / cols);
        return {row: r, col: c, x: c, y: r};
    },

    getPassedFields: function(mo) {
        if (!mo) console.error('No motion given');
        const positions = mo.getPassedPositions();
        // console.log(positions);
        const fields = [];
        for (const posKey in positions) {
            if (positions.hasOwnProperty(posKey)) {
                const pos = positions[posKey];
                const x = pos.get('x');
                const y = pos.get('y');
                if (this.withinBounds({x: x, y: y})) {
                    fields.push(this.getFieldAtRowCol(y, x));
                } else {
                    fields.push('_');
                }
            }
        }
        return fields;
    },
    isPossible: function(mo) {
        const fields = this.getPassedFields(mo);

        // if undefined in fields, not possible
        if (fields.indexOf(undefined) >= 0) return false;

        // concat fields and test against offroad regexp
        return (!fields.join('').match(this.offroadRegEx));
    },
    /**
     * @param motions
     * @returns {Array} Motions
     */
    verifiedMotions: function(motions) {
        const remaining = [];
        for (let p = 0; p < motions.length; p++) {
            const mo = motions[p];
            if (this.isPossible(mo)) {
                remaining.push(mo);
            }
        }
        return remaining;
    },
});
