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
        'userinfo': '#userInfoBar',
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
    },
});
