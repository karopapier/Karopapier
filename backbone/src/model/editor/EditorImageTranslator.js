const _ = require('underscore');
const Backbone = require('backbone');
const EditorImageTranslatorSettings = require('./EditorImageTranslatorSettings');
const MapRenderPalette = require('../map/MapRenderPalette');

module.exports = Backbone.Model.extend({
    initialize: function(options) {
        options = options || {};
        if (!options.map) {
            console.error('No map passed to EditorImageTranslator');
            return;
        }
        if (!options.editorsettings) {
            console.error('No editorsettings passed to EditorImageTranslator');
            return;
        }
        this.map = options.map;
        this.editorsettings = options.editorsettings;

        _.bindAll(this, 'loadImage', 'loadUrl', 'getImageData', 'getFieldForRgbaArray', 'initColorMode');
        // internal offscreen img and canvas
        this.image = new Image();
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.settings = new EditorImageTranslatorSettings();

        this.listenTo(this.settings, 'change', this.mapcodeResize);
        this.findOptions = {
            binary: true,
            invert: false,
            colors: ['X', '1'],
        };

        this.helper = 0;
        console.info('Dörtiii');
        this.initColorMode(this.map, new MapRenderPalette());
    },

    getFieldForRgbaArray: function(rgba, colormode) {
        if (!colormode) {
            let avg = (rgba[0] + rgba[1] + rgba[2]) / 3;
            let idx = (!this.findOptions.invert ^ !(avg <= 127)) << 0; // true =1, false =0
            field = this.findOptions.colors[idx];
            return field;
        }

        // full color mode
        let minDiff = Infinity;
        let field = '.';

        let fieldHSL = this.rgb2hsl(rgba);
        for (let f in this.hsls) {
            let diff = 0;
            let hsl = this.hsls[f];
            // console.log("Diff", hsl, fieldHSL);

            diff += Math.pow(hsl[0] - fieldHSL[0], 2);
            diff += Math.pow(hsl[1] - fieldHSL[1], 2);
            diff += Math.pow(hsl[2] - fieldHSL[2], 2);

            if (diff < minDiff) {
                minDiff = diff;
                field = f;
            }
        }

        return field;
    },

    processField: function(row, col, tr, tc, x, y, w, h, scW, scH, withTimeout) {
        // console.log("Processing", row, col, x, y, w, h, scW, scH);
        // console.log("processing ",x,"/",w,"and",y,"/",h);
        let me = this;
        let imgdata = me.ctx.getImageData(x, y, scW, scH);

        let pixelRgba = me.averageRgba(imgdata.data);
        let field = me.getFieldForRgbaArray(pixelRgba, !this.findOptions.binary);
        me.map.setFieldAtRowCol(row, col, field);

        if (!withTimeout) return false;

        // So we need to call the process for the next field ourselves...

        // next column
        x += scW;
        col += 1;
        if (col >= tc) {
            // col 0, but next row
            x = 0;
            col = 0;
            y += scH;
            row++;
        }

        if (row >= tr) {
            // console.log("DONE");
            this.editorsettings.set('undo', true);
            return true;
        }

        window.setTimeout(function() {
            me.processField(row, col, tr, tc, x, y, w, h, scW, scH, true);
        }, 0);
    },

    timecheck: function() {
        let start0 = new Date().getTime();
        let scW = this.settings.get('scaleWidth');
        let scH = this.settings.get('scaleHeight');

        this.processField(0, 0, 1, 1, 0, 0, scW, scH, scW, scH, false);
        let end0 = new Date().getTime();
        let t = Math.round(end0 - start0);
        // console.log(t);
        return t;
    },

    initColorMode: function(map, palette) {
        let whitelist = /(O|P|G|L|N|T|V|W|X|Y|Z)/;
        this.hsls = {};
        for (let f in map.FIELDS) {
            if (f.match(whitelist)) {
                let mainRGB = palette.get(f).split(',').map(function(e) {
                    return parseInt(e);
                });
                let hsl = this.rgb2hsl(mainRGB);
                this.hsls[f] = hsl;
            }
        }
        return true;
    },

    run: function() {
        this.editorsettings.set('undo', false);
        this.helper = 0;
        this.mapcodeResize();
        let scW = this.settings.get('scaleWidth');
        let scH = this.settings.get('scaleHeight');
        let w = this.canvas.width;
        let h = this.canvas.height;
        let t = this.settings.get('fieldtime');
        let tr = this.settings.get('targetRows');
        let tc = this.settings.get('targetCols');
        if (t == 0) {
            t = 20;
        }

        this.findOptions = {
            binary: this.settings.get('binary'),
            invert: this.settings.get('invert'),
            colors: [
                this.editorsettings.get('buttons')[1],
                this.editorsettings.get('buttons')[3],
            ],
        };

        // console.log("Run translation of " + w + "x" + h + " at", scW, scH, "with fieldtime", t);
        let me = this;
        let row = 0;
        let col = 0;

        // Speedmode -> Blocking the browser, run in blocking thread
        if (this.settings.get('speedmode')) {
            for (let y = 0; y < h; y += scH) {
                for (let x = 0; x < w; x += scW) {
                    me.processField(row, col, tr, tc, x, y, w, h, scW, scH, false);
                    col++;
                }
                col = 0;
                row++;
            }
            this.editorsettings.set('undo', false);
        } else {
            me.processField(0, 0, tr, tc, 0, 0, w, h, scW, scH, t);
        }

        // mapcode = codeRows.join('\n');
        // console.log(mapcode);
        // this.set("mapcode", mapcode);
        return true;
    },

    mapcodeResize: function() {
        // console.log("Resize map to", this.settings.get("targetCols"), this.settings.get("targetRows"));
        let undo = this.editorsettings.get('undo');
        this.editorsettings.set('undo', false);
        let row = new Array(this.settings.get('targetCols') + 1).join('.');
        let rows = [];
        for (let i = 0, l = this.settings.get('targetRows'); i < l; i++) {
            rows.push(row);
        }
        this.map.setMapcode(rows.join('\n'));
        this.editorsettings.set('undo', undo);
    },

    getSourceInfo: function() {
        return {
            width: this.image.width,
            height: this.image.height,
        };
    },

    loadImage: function(img) {
        let w = img.width;
        let h = img.height;
        // console.log("Loaded img", w, h);

        // adjust internal canvas
        this.canvas.width = w;
        this.canvas.height = h;
        this.ctx.drawImage(img, 0, 0);
        // console.log(this.canvas);
        // console.log(this.ctx);
        // console.log("Set new wh", w, h);
        this.settings.set({
            sourceWidth: w,
            sourceHeight: h,
        });
        // console.log("Set new wh done");
        // console.log("Loaded, set active true");
        this.settings.set('active', true);
        // console.log("Active is true");
        this.editorsettings.set('undo', false);
        this.settings.set('fieldtime', this.timecheck());
        this.editorsettings.set('undo', true);
    },

    getImageData: function() {
        // console.log("get data of ctx", this.canvas.width, this.canvas.height);
        return this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
    },

    loadUrl: function(url, callback) {
        let me = this;
        this.image.onload = function() {
            let w = me.image.width;
            let h = me.image.height;
            me.settings.set({
                sourceWidth: w,
                sourceHeight: h,
            });

            me.canvas.width = w;
            me.canvas.height = h;
            me.ctx.drawImage(me.image, 0, 0);
            callback();
        };
        this.image.src = url;
    },

    averageRgba: function(imageData) {
        if (imageData.length % 4 != 0) {
            console.error('Imagedate has a length of', imageData.length);
            return false;
        }

        let sum = [0, 0, 0];
        for (let p = 0, l = imageData.length; p < l; p += 4) {
            sum[0] += imageData[p];
            sum[1] += imageData[p + 1];
            sum[2] += imageData[p + 2];
        }
        let pixels = l / 4;
        avg = [sum[0] / pixels, sum[1] / pixels, sum[2] / pixels, 255];
        // console.log(avg);
        return avg;
    },

    rgb2hsl: function(rgb) {
        /**
         * based on
         * http://stackoverflow.com/questions/2353211/hsl-to-rgb-color-conversion
         * and adjusted
         *
         * Converts an RGB color value to HSL. Conversion formula
         * adapted from http://en.wikipedia.org/wiki/HSL_color_space.
         * Assumes r, g, and b are contained in the set [0, 255] and
         * returns h, s, and l in the set [0, 1].
         *
         * @param   Array           [red, green, blue]
         * @return  Array           The HSL representation
         */
        let r = rgb[0];
        let g = rgb[1];
        let b = rgb[2];
        r /= 255, g /= 255, b /= 255;
        const max = Math.max(r, g, b);
        const min = Math.min(r, g, b);
        let h = 0;
        let s = 0;
        let l = (max + min) / 2;

        if (max === min) {
            h = s = 0; // achromatic
        } else {
            let d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            switch (max) {
                case r:
                    h = (g - b) / d + (g < b ? 6 : 0);
                    break;
                case g:
                    h = (b - r) / d + 2;
                    break;
                case b:
                    h = (r - g) / d + 4;
                    break;
            }
            h *= 60;
        }
        s *= 100;
        l *= 100;

        return [Math.round(h), Math.round(s), Math.round(l)];
    },
});
