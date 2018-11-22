module.exports = Marionette.View.extend({
    tagName: 'pre',
    initialize(options) {
        this.info = options.info || '-';
        this.log('Init');
    },
    log(t) {
        const d = new Date();
        const h = d.getHours();
        let m = d.getMinutes();
        let s = d.getSeconds();
        const ms = d.getMilliseconds();
        m = (m < 10) ? '0' + m : m;
        s = (s < 10) ? '0' + s : s;
        const ds = h + ':' + m + ':' + s + '.' + ms;
        this.$el.append(ds + ' ' + this.info + ' ' + t + ' (' + this.cid + ')\n');
    },
    render() {
        this.log('Render');
        return this;
    },
});
