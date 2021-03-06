const _ = require('underscore');
const Marionette = require('backbone.marionette');
const PlayerMovesView = require('./PlayerMovesView');

module.exports = Marionette.CollectionView.extend({
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
    childView: PlayerMovesView,
    childViewOptions(el, i) {
        // pass the right playersMoves into the view
        // console.log("get moves for ", el.get("id"));
        // console.log(this.playersMoves[el.get("id")]);
        return {
            collection: this.playersMoves[el.get('id')],
            settings: this.settings,
            util: Karopapier.util,
        };
    },
    viewComparator(a, b) {
        // for the view, make "myself" and highlighted always last to be svg-rendered on top
        if (a.get('highlight')) return 1;
        if (a.get('id') === this.user.get('id')) return 1;
        // console.log("View Compa", a.get("id"), b.get("id"), "vs", this.user.get("id"));
        // console.log("View Compa", a.get("highlight"), b.get("highlight"), "highlight");
    },
    initialize(options) {
        options = options || {};

        if (!this.collection) {
            console.error('Missing Player Collection');
            return false;
        }

        if (!options.settings) {
            console.error('Missing settings in PlayersMovesView');
            return false;
        }
        this.settings = options.settings;

        if (!options.playersMoves) {
            console.error('Missing playersMoves in PlayersMovesView');
            return false;
        }
        this.playersMoves = options.playersMoves;

        if (!options.user) {
            console.error('No user passed into PlayersMovesView');
            return false;
        }
        this.user = options.user;

        if (!options.map) {
            console.error('No map passed into PlayersMovesView');
            return false;
        }
        this.map = options.map;


        _.bindAll(this, 'check', 'resize');
        this.listenTo(this.settings, 'change:size change:border', this.resize);
        this.listenTo(this.user, 'change:id', this.check);
        this.listenTo(this.map, 'change:cols chang:rows', this.resize);
        this.listenTo(this.settings, 'change:drawLimit', this.drawLimit);
        this.listenTo(this.collection, 'update', this.check);
        this.listenTo(this.collection, 'change:highlight', this.reorder);
        this.resize();
    },
    check() {
        const me = this;
        // console.info("CHECK");
        this.resize();

        // initialise visibility & drawLimits
        this.collection.each((m) => {
            // defaults
            let drawLimit = 5;
            let visible = true;

            // console.warn("Determine limit for player; Me? Finished? Modified?");
            // Me?
            if (m.get('id') == me.user.get('id')) {
                drawLimit = -1;
            }

            // finished?
            if (m.get('position') > 0) {
                drawLimit = -1;
                visible = false;
            }

            if (m.get('highlight')) {
                visible = true;
            }

            // setting player
            // console.warn("Setting player", m, visible);
            m.set({
                drawLimit,
                initDrawLimit: drawLimit,
                visible,
            });
        });
        // this.render();
    },

    drawLimit() {
        const newLimit = this.settings.get('drawLimit');
        this.collection.each((m) => {
            m.set('drawLimit', newLimit);
        });
    },

    resize() {
        this.fieldSize = (this.settings.get('size') + this.settings.get('border'));
        const w = this.map.get('cols') * this.fieldSize;
        const h = this.map.get('rows') * this.fieldSize;
        this.$el.css({width: w, height: h}).attr({width: w, height: h});
    },
})
;
