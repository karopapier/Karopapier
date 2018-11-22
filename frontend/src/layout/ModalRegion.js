const $ = require('jquery');
const Marionette = require('backbone.marionette');
module.exports = Marionette.Region.extend({
    events: {
        'click .modal-close': 'removeView',
    },

    attachHtml(view) {
        $('body').addClass('noscroll');
        const modalEl = $('<div class="modal-content"></div>');
        modalEl.append(view.el);
        this.$el.empty().
            append('<div class="modal-overlay">').
            // append('<div class="modal-close clickable">X</div>').
            append(modalEl);
        this.$el.removeClass('hidden');
    },

    removeView(view) {
        this.$el.addClass('hidden').empty();
        $('body').removeClass('noscroll');
    },
});
