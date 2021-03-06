const Backbone = require('backbone');
const KaroUtil = require('../../model/Util');
const _ = require('underscore');

module.exports = Backbone.View.extend({
    optionDefaults: {
        visible: true,
    },
    initialize(options) {
        _.bindAll(this, 'resize', 'hidePlayerInfo', 'showPlayerInfo');
        // console.info("MapPlayerMoves being called for", this.model.get("name"));
        // console.log(options);
        options = options || {};
        if (!options.settings) {
            console.error('No settings for MapPlayerMoves');
        }

        this.settings = options.settings;
        this.w = options.w;
        this.h = options.h;
        this.listenTo(this.settings, 'change:size change:border ', this.render);
        this.listenTo(this.model, 'change:drawLimit', this.render);
        this.listenTo(this.model, 'change:visible', this.visibility);
        this.listenTo(this.model, 'change:highlight', this.highlight);
        this.color = '#' + this.model.get('color');
    },

    events: {
        'mouseenter .playerPosition': 'showPlayerInfo',
        'mouseleave .playerPosition': 'hidePlayerInfo',
    },

    render() {
        if (!this.g) {
            this.createGroup();
        } else {
            this.$el.empty();
        }
        this.resize();
        this.addMoves();
        this.addPosition();
        this.visibility();
    },

    createGroup() {
        // replace my container div with a svg group
        this.g = KaroUtil.createSvg('g', {
            class: 'playerMoves',
        });
        this.setElement(this.g);
    },

    addPosition() {
        // if no move, nothing to draw, stop
        if (this.model.moves.length < 1) return false;

        const m = this.model.getLastMove();
        const currentPosition = KaroUtil.createSvg('circle', {
            'cx': m.get('x') * this.fieldsize + this.size / 2,
            'cy': m.get('y') * this.fieldsize + this.size / 2,
            'r': 4,
            // stroke: "black",
            'fill': this.color,
            'class': 'playerPosition',
            'data-playerId': this.model.get('id'),
        });
        this.$el.append(currentPosition);
    },

    addMoves() {
        // if only one move, stop here
        if (this.model.moves.length <= 1) return false;

        const limit = this.model.get('drawLimit');
        // console.log("Limit:", limit);
        const color = this.color;
        const movesFragment = document.createDocumentFragment();

        let moves = this.model.moves.toArray();
        if (limit >= 0) {
            // reduce moves to limited amount
            moves = this.model.moves.last(limit + 1);
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
                const square = KaroUtil.createSvg('rect', {
                    x: x * this.fieldsize + this.thirdsize,
                    y: y * this.fieldsize + this.thirdsize,
                    width: this.thirdsize,
                    height: this.thirdsize,
                    fill: color,
                });
                movesFragment.appendChild(square);
            });
        }

        // console.log(pathCode);
        const p = KaroUtil.createSvg('path', {
            'd': pathCode,
            'stroke': color,
            'stroke-width': 1,
            'fill': 'none',
        });
        // movesFragment.appendChild(p);
        // console.log(movesFragment);
        this.$el.append(movesFragment);
        this.$el.append(p);
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
        // console.log(this.model.attributes);
        if (this.model.get('visible')) {
            this.$el.css('display', 'inline');
        } else {
            this.$el.css('display', 'none');
        }
    },

    highlight() {
        if (this.model.get('highlight')) {
            this.model.set('drawLimit', -1);
        } else {
            this.model.set('drawLimit', this.model.get('initDrawLimit'));
        }
    },

    showPlayerInfo(e) {
        this.model.set('highlight', true);
        return true;
        const playerId = e.currentTarget.getAttribute('data-playerId');
        const p = this.collection.get(playerId);
        this.activePi = new PlayerInfo({
            model: p,
        });
        this.activePi.render();
        this.$el.parent().append(this.activePi.el);
    },
    hidePlayerInfo(e) {
        this.model.set('highlight', false);
        // this.activePi.remove();
    },
    old_render() {
        this.el.appendChild(movesFragment);
        this.el.appendChild(posFragment);
    },
});
