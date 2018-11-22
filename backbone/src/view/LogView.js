module.exports = Marionette.View.extend({
    tagName: 'pre',
    initialize(options) {
        this.info = options.info || '-';
        this.log('Init');
    },
    log(t) {
        let d = new Date();
        let h = d.getHours();
        let m = d.getMinutes();
        let s = d.getSeconds();
        let ms = d.getMilliseconds();
        m = (m < 10) ? '0' + m : m;
        s = (s < 10) ? '0' + s : s;
        let ds = h + ':' + m + ':' + s + '.' + ms;
        this.$el.append(ds + ' ' + this.info + ' ' + t + ' (' + this.cid + ')\n');
    },
    render() {
        this.log('Render');
        return this;
    },
});
