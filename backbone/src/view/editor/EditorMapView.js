const _ = require('underscore');
const Backbone = require('backbone');
const MapRenderView = require('../map/MapRenderView');

module.exports = Backbone.View.extend({
    initialize(options) {
        options = options || {};
        if (!options.viewsettings) {
            console.error('No viewsettings passed to EditorMapView');
            return;
        }
        if (!options.editorsettings) {
            console.error('No editorsettings passed to EditorMapView');
            return;
        }

        _.bindAll(this, 'render', 'draw', 'mousedown', 'mouseup', 'mousemove', 'mouseleave', 'recalcDimensions');
        this.viewsettings = options.viewsettings;
        this.editorsettings = options.editorsettings;
        this.resizeHandleWidth = 15;
        this.listenTo(this.model, 'change:mapcode', this.recalcDimensions);

        this.buttonDown = [false, false, false, false];
        this.drawing = false;
        this.resizing = false;
    },
    render() {
        this.mapRenderView = new MapRenderView({
            settings: this.viewsettings,
            model: this.model,
        });
        this.listenTo(this.mapRenderView, 'render', this.recalcDimensions);
        this.setElement(this.mapRenderView.el);
        this.$el.css('border', this.resizeHandleWidth + 'px solid lightgrey');
        this.mapRenderView.render();
    },

    events: {
        'mouseleave': 'mouseleave',
        'mouseenter': 'mouseenter',
        'mousedown': 'mousedown',
        'mouseup': 'mouseup',
        'mousemove': 'mousemove',
        'contextmenu': 'rightclick',
    },

    rightclick(e) {
        if (this.editorsettings.get('rightclick')) {
            e.preventDefault();
            return false;
        }
    },

    xyFromE(e) {
        const x = (e.pageX - this.offLeft);
        const y = (e.pageY - this.offTop);
        return {x, y};
    },

    draw(e) {
        const xy = this.xyFromE(e);
        const x = xy.x - this.resizeHandleWidth;
        const y = xy.y - this.resizeHandleWidth;
        const buttons = this.editorsettings.get('buttons');
        // console.log("Draw ", x, y);
        for (let i = 1; i <= 3; i++) {
            if (this.buttonDown[i]) {
                this.mapRenderView.setFieldAtXY(x, y, buttons[i]);
            }
        }
    },

    resize(e) {
        if (!this.resize) return false;
        const xy = this.xyFromE(e);

        // check for W-E resize
        if (this.currentDirections.we) {
            const x = xy.x - this.resizeHandleWidth;
            const right = Math.floor((x - this.startX) / this.fieldsize) > 0;
            const left = Math.ceil((x - this.startX) / this.fieldsize) < 0;

            if (this.currentDirections.e) {
                if (right) {
                    this.model.addCol(1);
                    this.startX += this.fieldsize;
                }

                if (left) {
                    this.model.delCol(1);
                    this.startX -= this.fieldsize;
                }
            }

            if (this.currentDirections.w) {
                if (left) {
                    this.model.addCol(1, 0);
                    this.startX -= this.fieldsize;
                }

                if (right) {
                    this.model.delCol(1, 0);
                    this.startX += this.fieldsize;
                }
            }
        } else {
            // console.log("Skip WE");
        }

        // check for N-S resize
        if (this.currentDirections.ns) {
            const y = xy.y - this.resizeHandleWidth;
            const down = Math.floor((y - this.startY) / this.fieldsize) > 0;
            const up = Math.ceil((y - this.startY) / this.fieldsize) < 0;

            if (this.currentDirections.s) {
                if (down) {
                    this.model.addRow(1);
                    this.startY += this.fieldsize;
                }

                if (up) {
                    this.model.delRow(1);
                    this.startY -= this.fieldsize;
                }
            }

            if (this.currentDirections.n) {
                if (up) {
                    this.model.addRow(1, 0);
                    this.startY -= this.fieldsize;
                }

                if (down) {
                    this.model.delRow(1, 0);
                    this.startY += this.fieldsize;
                }
            }
        }
    },

    recalcDimensions(e) {
        this.w = this.$el.width();
        this.h = this.$el.height();
        const off = this.$el.offset();
        this.offLeft = Math.round(off.left);
        this.offTop = Math.round(off.top);
        this.outW = this.$el.outerWidth();
        this.outH = this.$el.outerHeight();
        // console.log("Now", this.w, this.h, this.outW, this.outH, this.offLeft, this.offTop);
    },

    resizeDirections(e) {
        const d = {
            we: '',
            ns: '',
            n: false,
            s: false,
            w: false,
            e: false,
        };
        const xy = this.xyFromE(e);
        const x = xy.x;
        const y = xy.y;
        const rhw = this.resizeHandleWidth;
        const w = this.w;
        const h = this.h;

        if (x < rhw) {
            d.we = 'w';
            d.w = true;
        }
        if (x > (w + rhw)) {
            d.we = 'e';
            d.e = true;
        }

        if (y < rhw) {
            d.ns = 'n';
            d.n = true;
        }
        if (y > (h + rhw)) {
            d.ns = 's';
            d.s = true;
        }

        d.direction = d.ns + d.we;
        return d;
    },

    mousedown(e) {
        const button = e.which;
        // console.log("Button", button, "right", this.editorsettings.get("rightclick"));
        if ((button == 3) && (!this.editorsettings.get('rightclick'))) {
            // leave default rightclick menu intact
            return true;
        }

        this.currentDirections = this.resizeDirections(e);
        this.fieldsize = this.viewsettings.get('size') + this.viewsettings.get('border');
        // console.log(this.fieldsize);

        this.editorsettings.set('undo', false);

        this.buttonDown[e.which] = true;
        const xy = this.xyFromE(e);
        // check if we are resizing
        if (this.currentDirections.direction !== '') {
            this.startX = xy.x - this.resizeHandleWidth;
            this.startY = xy.y - this.resizeHandleWidth;
            this.resizing = true;
            e.preventDefault();

            $(document).bind('mousemove', _.bind(this.mousemove, this));
            $(document).bind('mouseup', _.bind(this.mouseup, this));

            return false;
        }

        // no resize, start drawing

        // check draw mode
        if (this.editorsettings.get('drawmode') == 'floodfill') {
            // console.log("FLOODFILL");
            const x = xy.x - this.resizeHandleWidth;
            const y = xy.y - this.resizeHandleWidth;
            const buttons = this.editorsettings.get('buttons');
            // console.log(this.buttonDown)

            for (let i = 1; i <= 3; i++) {
                // console.log(this.buttonDown)
                if (this.buttonDown[i]) {
                    // console.log("Floodfill", x, y, buttons[i]);
                    this.mapRenderView.floodfill(x, y, buttons[i]);
                }
            }
            return true;
        }

        // default draw mode
        this.drawing = true;
        // this.render();
        this.draw(e);
        return true;
    },

    mouseup(e) {
        this.editorsettings.set('undo', true);
        this.drawing = false;
        this.resizing = false;
        this.buttonDown[e.which] = false;
        $(document).unbind('mousemove');
        $(document).unbind('mouseup');
    },

    mouseenter(e) {
        // If it's not correctly updated, do this!
        if (this.offTop == 0) this.recalcDimensions();
    },

    mousemove(e) {
        if (this.drawing) {
            this.draw(e);
            return true;
        }

        if (this.resizing) {
            this.resize(e);
            return true;
        }

        if (e.target.tagName.toUpperCase() !== 'CANVAS') return false;
        // console.log(e.target);

        // simple mouse move
        const d = this.resizeDirections(e);
        if (d.direction) {
            this.el.style.cursor = d.direction + '-resize';
        } else {
            this.el.style.cursor = 'crosshair';
        }
    },

    mouseleave(e) {
        // console.log("LEAVE");
        this.drawing = false;
        // this.resizing = false;
        for (let i = 1; i <= 3; i++) {
            this.buttonDown[e.which] = false;
        }
    },

});
