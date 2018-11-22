const Backbone = require('backbone');

module.exports = Backbone.Model.extend({
    getPath(mapid, cb) {
        let rows = 0;
        let cols = 0;
        let path = '';

        const amifinished = function() {
            // console.log("check if finished");
            // we should have the path and dimensions here
            // console.log(path);
            if (path === false) {
                // console.warn("I stop this!!!")
                cb(false);
                return false;
            }
            if (rows != 0 && cols != 0 && path != '') {
                const m = {};
                m.r = rows;
                m.c = cols;

                const xml = (new XMLSerializer).serializeToString(path);
                m.p = xml;
                store.set('map' + mapid, m);
                cb(m);
            } else {
                // console.log("Fehlt noch was");
            }
        };

        // check if we have the path in store
        const i = store.get('map' + mapid);
        if (i) {
            // console.log ("I from store",i);
            cb(i);
        } else {
            // we need to get the path and dimensions via request
            $.get('/paths/' + mapid + '.svg', (data) => {
                // console.debug(data);
                path = data.getElementById('mapSvgView');
                amifinished();
            }).fail((err) => {
                console.error(err);
                path = false;
                amifinished();
            });
            $.getJSON('/api/map/' + mapid + '.json', (data) => {
                // console.log(data);
                rows = data.rows;
                cols = data.cols;
                amifinished();
            });
        }
    },
    getFromUrl(id) {
    },
    getFromStore(id) {
    },
    saveToStore(id, path) {
    },
});
