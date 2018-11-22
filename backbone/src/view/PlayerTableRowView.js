const _ = require('underscore');
const Backbone = require('backbone');

module.exports = Backbone.View.extend({
    tagName: 'tr',
    className: 'playerTableRow',
    template: window['JST']['game/playerTableRow'],
    initialize() {
        _.bindAll(this, 'render');
        // this.listenTo(this.collection, "change", this.render);
        // this.listenTo(this.collection, "reset", this.render);
        this.listenTo(this.model, 'change:visible', this.updateVisibility);
        this.listenTo(this.model, 'change:highlight', this.updateHighlight);
    },

    events: {
        'change input': 'setVisibility',
        'mouseenter': 'highlight',
        'mouseleave': 'unhighlight',
    },

    setVisibility(e) {
        $e = $(e.currentTarget);
        this.model.set('visible', $e.prop('checked'));
    },

    updateVisibility() {
        this.$('input').prop('checked', this.model.get('visible'));
    },

    updateHighlight() {
        if (this.model.get('highlight')) {
            this.$el.addClass('highlight');
        } else {
            this.$el.removeClass('highlight');
        }
    },

    highlight() {
        this.model.set('highlight', true);
    },

    unhighlight() {
        this.model.set('highlight', false);
    },

    render() {
        const data = this.model.toJSON();
        let statusClass = '';
        let displayStatus = '';
        const status = this.model.get('status');
        const pos = this.model.get('position');
        if (status == 'kicked' || status == 'left') {
            statusClass = status;
            displayStatus = this.model.getStatus();
        }

        if (status === 'ok') {
            if (this.model.get('dran')) {
                statusClass = 'dran';
                displayStatus = 'dran';
            } else {
                if (pos != 0) {
                    displayStatus = 'wurde ' + pos + '.';
                } else {
                    if (this.model.get('moved')) {
                        displayStatus = 'war schon';
                        statusClass = 'moved';
                    } else {
                        displayStatus = 'kommt noch';
                        statusClass = 'tomove';
                    }
                }
            }
        }

        data.lastmovetime = this.model.getLastMove() ? this.model.getLastMove().get('t') : '-';
        data.displayStatus = displayStatus;
        data.statusClass = statusClass;
        this.$el.html(this.template(data));
        return this;
    },
});
