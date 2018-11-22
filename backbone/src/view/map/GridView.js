// const Marionette = require('backbone.marionette');
const Backbone = require('backbone');
module.exports = Backbone.View.extend(/** @lends GridView.prototype */{
    /* this makes it generate namespaced SVG tags */
    _createElement(tagName) {
        return document.createElementNS('http://www.w3.org/2000/svg', tagName);
    },
    tagName: 'svg',
    optionDefaults: {
        size: 11,
        border: 1,
        drawMoveLimit: 2,
        visible: true,
    },
    /**
     * @class GridView
     * @constructur GridView
     * @param options
     * @returns {boolean}
     */
    initialize(options) {
        options = options || {};

        if (!options.players) {
            console.error('Missing Player Collection in GridView');
            return false;
        }
        this.players = options.players;

        if (!options.settings) {
            console.error('Missing settings in GridView');
            return false;
        }
        this.settings = options.settings;

        if (!options.user) {
            console.error('No user passed into GridView');
            return false;
        }
        this.user = options.user;

        if (!options.map) {
            console.error('No map passed into GridView');
            return false;
        }
        this.map = options.map;

        this.listenTo(this.map, 'change:rows change:cols', this.resize);
        this.listenTo(this.settings, 'change:size change:border', this.resize);
        this.listenTo(this.user, 'change:id', this.check);
        this.listenTo(this.settings, 'change:drawLimit', this.drawLimit);
        this.listenTo(this.players, 'change add remove reset', this.drawPositions);
        this.resize();
    },
    events: {
        'contextmenu': 'contextmenu',
        'click': 'leftclick',
    },

    contextmenu(e) {
        this.trigger('contextmenu', e);
        e.preventDefault();
    },

    leftclick(e) {
        this.trigger('default');
    },

    drawPositions() {
        // console.log("DRAW POSITIONS");
        this.fieldsize = this.settings.get('size') + this.settings.get('border');
        this.players.each((p) => {
            const x = p.get('lastmove').x;
            const y = p.get('lastmove').y;
            const color = '#' + p.get('color');
            const pos = this._createElement('circle');
            const attrs = {
                cx: x * this.fieldsize + this.fieldsize / 2,
                cy: y * this.fieldsize + this.fieldsize / 2,
                r: this.fieldsize * .3,
                fill: color,
            };
            for (const k in attrs) pos.setAttribute(k, attrs[k]);
            this.$el.append(pos);
        });
    },

    resize() {
        this.fieldSize = (this.settings.get('size') + this.settings.get('border'));
        const w = this.map.get('cols') * this.fieldSize;
        const h = this.map.get('rows') * this.fieldSize;
        this.$el.css({width: w, height: h}).attr({width: w, height: h});
    },
});
