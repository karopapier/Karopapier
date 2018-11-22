const Backbone = require('backbone');
const KRACHZ = require('../../model/KRACHZ');
const PossibleView = require('./PossibleView');
const _ = require('underscore');

module.exports = Backbone.View.extend({
    events: {
        'clicked': 'clickMove',
    },
    initialize(options) {
        // console.warn("I AM THE POSSIBLES VIEW");
        _.bindAll(this, 'clearPossibles', 'checkWillCrash', 'render');
        if (!options.hasOwnProperty('game')) {
            console.error('No game for PossiblesView');
        }
        if (!options.hasOwnProperty('mapView')) {
            console.error('No mapView for PossiblesView');
        }
        this.game = options.game;
        this.mapView = options.mapView;
        // grabbing settings from the mapview to listen to size change
        this.settings = this.mapView.settings;
        this.listenTo(this.game.possibles, 'reset', this.render);
        this.listenTo(this, 'changeHighlight', this.checkHighlight);
        this.highlight = false;
    },
    clearPossibles() {
        // console.info("Clear possibles");
        _.each(this.views, (v) => {
            // console.log("Ich entferne nen alten possible");
            v.cleanup().remove();
        });
        this.views = [];
        // this.$('.possibleMove').remove();
    },
    clickMove(mo) {
        // forward the event
        this.trigger('game:player:move', this.game.get('dranId'), mo);
    },
    checkWillCrash(k, possible) {
        possible.set('willCrash', k.willCrash(possible, 16));
    },
    checkHighlight(e, a, b) {
        // console.log("Triggered");
        if (this.highlight) {
            this.highlight.model.set('highlight', false);
        }
        e.model.set('highlight', true);
        this.highlight = e;
    },
    render() {
        // console.info("Rendering possibles for",this.game.get("id"));
        this.clearPossibles();
        // console.log(possibles);

        let k = new KRACHZ({
            map: this.game.map,
        });

        this.game.possibles.each((possible) => {
            let posView = new PossibleView({
                mapView: this.mapView,
                model: possible,
                parent: this,
            }).render();
            setTimeout(this.checkWillCrash.bind(this, k, possible), 0);
            // console.log(posView.el);
            // console.log(this.$el);
            this.$el.append(posView.el);
            this.views.push(posView);
            this.listenTo(posView, 'clicked', this.clickMove.bind(this));
        });
    },
});
