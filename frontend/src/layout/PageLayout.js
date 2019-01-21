const Marionette = require('backbone.marionette');
const ModalRegion = require('./ModalRegion');

module.exports = Marionette.View.extend({
    template() {
        return;
    },

    regions: {
        'header-preview': {
            el: '.header-dran-preview',
            replaceElement: true,
        },
        'userinfo': '.header-user-infobar',
        'content': '.content',
        'modal': {
            el: '.modal-container',
            regionClass: ModalRegion,
        },
        'footer': 'footer',
        'mobile-nav': {
            el: '.mobile-nav',
            replaceElement: true,
        },
        'navi': {
            el: '.top-nav',
            replaceElement: true,
        },

    },

    events: {
        'click a.js-applink': 'navigate',
        // 'click a.js-logout': 'logout',
    },

    navigate(e) {
        if (e.ctrlKey || e.shiftKey) {
            return true;
        }
        this.trigger('navigate', e.currentTarget.href);
        return e.preventDefault();
    },
});
