const Marionette = require('backbone.marionette');
const ModalRegion = require('./ModalRegion');

module.exports = Marionette.View.extend({
    template() {
        return;
    },
    regions: {
        userinfo: '#userInfoBar',
        content: '.content',
        modal: {
            el: '.modal-container',
            regionClass: ModalRegion,
        },
    },
});
