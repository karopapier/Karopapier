const _ = require('underscore');
const Marionette = require('backbone.marionette');

module.exports = Marionette.ItemView.extend({
    tagName: 'canvas',
    initialize(options) {
        _.bindAll(this, 'drop');
        options = options || {};
        if (!options.imageTranslator) {
            console.error('No imageTranslator passed to EditorImageTranslatorPreview');
            return false;
        }

        this.imageTranslator = options.imageTranslator;
        this.canvas = this.el;
        this.ctx = this.canvas.getContext('2d');

        const me = this;
        this.img = new Image();
        this.img.onload = function() {
            // console.log("Cat loaded");
            const w = me.img.width;
            const h = me.img.height;

            // adjust internal canvas
            me.canvas.width = w;
            me.canvas.height = h;
            me.ctx.drawImage(me.img, 0, 0);
        };
        this.img.src = '/images/dragdropcat.png';

        this.imageTranslator.settings.set('active', false);
        this.listenTo(this.imageTranslator.settings, 'change', this.render);
    },

    events: {
        'dragover': 'prevent',
        'drop': 'drop',
    },

    prevent(e) {
        e.preventDefault();
        return false;
    },

    drop(e) {
        e.preventDefault();
        const origEvent = e.originalEvent;
        const me = this;
        const files = origEvent.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (typeof FileReader !== 'undefined' && file.type.indexOf('image') != -1) {
                const reader = new FileReader();
                // Note: addEventListener doesn't work in Google Chrome for this event
                reader.onload = function(e) {
                    me.img.src = e.target.result;
                    me.img.onload = function() {
                        me.imageTranslator.loadImage(me.img);
                    };
                };
                reader.readAsDataURL(file);
            }
        }
        // console.log("Set active");
        e.preventDefault();
    },

    render() {
        if (!this.imageTranslator.settings.get('active')) {
            // console.info("not active");
            return true;
        }
        this.canvas.width = this.imageTranslator.settings.get('sourceWidth');
        this.canvas.height = this.imageTranslator.settings.get('sourceHeight');
        const imgdat = this.imageTranslator.getImageData();
        // console.log(imgdat);
        this.ctx.putImageData(imgdat, 0, 0);

        console.info('Now add grid');
    },
});
