const Marionette = require('backbone.marionette');

module.exports = Marionette.ItemView.extend({
    tagName: 'g',
    /* this makes it generate namespaced SVG tags */
    _createElement(tagName) {
        return document.createElementNS('http://www.w3.org/2000/svg', tagName);
    },
    initialize(options) {
        options = options || {};
        if (!options.settings) {
            console.error('No settings for MapPlayerMoves');
            return false;
        }

        if (!options.util) {
            console.error('No utils for MapPlayerMoves');
            return false;
        }

        if (!options.model) {
            console.error('No model player for MapPlayerMoves');
            return false;
        }

        this.settings = options.settings;
        this.w = options.w;
        this.h = options.h;
        this.listenTo(this.settings, 'change:size change:border ', this.render);
        this.listenTo(this.model, 'change:drawLimit change:movesCount change:crashCount', this.render);
        this.listenTo(this.model, 'change:visible', this.visibility);
        this.listenTo(this.model, 'change:highlight', this.highlight);
        this.color = '#' + this.model.get('color');
    },

    render() {
        this.$el.empty();
        this.resize();
        this.addMoves();
        this.visibility();
    },

    addMoves() {
        // if only one move, stop here
        if (this.collection.length <= 1) return false;

        const limit = this.model.get('drawLimit');
        // console.log("Limit:", limit);
        const color = this.color;
        const movesFragment = document.createDocumentFragment();

        let moves = this.collection.toArray();
        if (limit >= 0) {
            // reduce moves to limited amount
            moves = this.collection.last(limit + 1);
        }

        if (this.model.get('position') > 0) {
            // finished, don't draw last line
            moves.pop();
        }
        // console.log("Render with moves", moves.length);

        let pathCode = '';
        if (moves.length > 0) {
            // start path for all moves
            pathCode = 'M' + (parseInt(moves[0].get('x') * this.fieldsize) + this.halfsize) + ',' + (parseInt(moves[0].get('y') * this.fieldsize) + this.halfsize); // eslint-disable-line max-len
            moves.forEach((m, i) => {
                const x = parseInt(m.get('x'));
                const y = parseInt(m.get('y'));
                pathCode += 'L' + (x * this.fieldsize + this.halfsize) + ',' + (y * this.fieldsize + this.halfsize);
                const square = this._createElement('rect');
                const attrs = {
                    x: x * this.fieldsize + this.thirdsize,
                    y: y * this.fieldsize + this.thirdsize,
                    width: this.thirdsize,
                    height: this.thirdsize,
                    fill: color,
                };
                for (const k in attrs) square.setAttribute(k, attrs[k]);
                movesFragment.appendChild(square);
            });
            // console.log(pathCode);
        }

        // console.log(pathCode);
        const p = this._createElement('path');
        const attrs = {
            'd': pathCode,
            'stroke': color,
            'stroke-width': 1,
            'fill': 'none',
        };
        for (const k in attrs) p.setAttribute(k, attrs[k]);
        // //movesFragment.appendChild(p);
        // console.log(movesFragment);
        this.el.appendChild(movesFragment);
        this.el.appendChild(p);
        // this.$el.append(movesFragment);
        // this.$el.append(p);
        // console.log("RENDERTE moves for", this.model.get("name"));
    },

    resize() {
        this.size = this.settings.get('size');
        this.halfsize = this.size / 2;
        this.thirdsize = this.size / 3;
        this.border = this.settings.get('border');
        this.fieldsize = this.size + this.border;
    },

    visibility() {
        if (this.model.get('visible') || (this.model.get('highlight'))) {
            this.$el.css('display', 'inline');
        } else {
            this.$el.css('display', 'none');
        }
    },

    highlight() {
        // console.log("Change highlight");
        if (this.model.get('highlight')) {
            this.model.set('drawLimit', -1);
        } else {
            this.model.set('drawLimit', this.model.get('initDrawLimit'));
        }
        // make sure to show the highlighted one
        this.visibility();
    },
});
