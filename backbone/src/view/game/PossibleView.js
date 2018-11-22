const Backbone = require('backbone');
const _ = require('underscore');
const $ = require('jquery');

module.exports = Backbone.View.extend({
    tagName: 'div',
    className: 'possibleMove',
    events: {
        'touchstart': 'wasTouch',
        'click': 'checkMove',
        'mouseenter': 'hoverMove',
        'mouseleave': 'unhoverMove',
        'click .confirmer': 'confirmMove',
    },
    NOWAY() {
        alert('YES');
    },
    initialize(options) {
        this.mouseOrTouch = 'mouse';
        _.bindAll(this, 'checkMove', 'hoverMove', 'unhoverMove', 'render', 'cleanup');
        if (!options.hasOwnProperty('mapView')) {
            console.error('No mapView for PossiblesView');
        }
        this.mapView = options.mapView;
        this.parent = options.parent;
        // grabbing settings from the mapview to listen to size change
        this.settings = this.mapView.settings;
        this.listenTo(this.model, 'change', this.render);

        this.$confirmer = $('<div class="confirmer" style="position: fixed; bottom: 20px; right:20px; width: 50px; height: 50px; background-color: red">' + this.model.get('vector').toString() + '</div>'); // eslint-disable-line max-len
        this.$el.append(this.$confirmer.hide());
    },
    wasTouch() {
        this.mouseOrTouch = 'touch';
    },
    confirmMove(e) {
        this.trigger('clicked', this.model);
        this.mouseOrTouch = 'mouse';
        e.stopPropagation();
    },
    checkMove(e) {
        console.log('Click by ', this.mouseOrTouch);
        if (this.mouseOrTouch == 'touch') {
            this.model.set('highlight', true);
            this.parent.trigger('changeHighlight', this);
        } else {
            // console.log("trigger", this.model);
            this.trigger('clicked', this.model);
        }
        this.mouseOrTouch = 'mouse';
    },
    hoverMove(e, a, b) {
        const mo = this.model;
        if (mo.get('vector').getLength() > 2.8) {
            // console.log(mo);
            const stop = mo.getStopPosition();
            this.stopDiv = $('<div class="stopPosition" style="left: ' + stop.get('x') * 12 + 'px; top: ' + stop.get('y') * 12 + 'px;"></div>'); // eslint-disable-line max-len
            this.$el.parent().append(this.stopDiv);
        }
    },
    unhoverMove() {
        if (this.stopDiv) this.stopDiv.remove();
    },
    cleanup() {
        this.unhoverMove();
        return this;
    },
    render() {
        const v = this.model.get('vector');
        const p = this.model.get('position');
        this.$el.css({
            left: p.get('x') * 12,
            top: p.get('y') * 12,
        }).attr({
            'title': this.model.get('vector').toString(),
            'data-motionString': this.model.toString(),
        });

        if (this.model.get('highlight')) {
            this.$el.addClass('highlight');
            this.$confirmer.show();
        } else {
            this.$el.removeClass('highlight');
            this.$confirmer.hide();
        }
        // if vector = (0|0], mark as start
        // console.log(v.toString());
        const willCrash = this.model.get('willCrash');
        if (willCrash !== undefined) {
            if (this.model.get('willCrash')) {
                this.$el.addClass('willCrash');
            } else {
                this.$el.addClass('noCrash');
            }
        }
        if (v.toString() === '(0|0)') {
            this.$el.attr('title', 'Start: ' + this.model.toKeyString());
            this.$el.addClass('isStart');
        }
        return this;
    },
});
