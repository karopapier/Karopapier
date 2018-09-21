const Backbone = require('backbone');
const Game = require('../model/Game');
const Radio = require('backbone.radio');
const dataChannel = Radio.channel('data');

module.exports = Backbone.Collection.extend({
    model: Game,

    url() {
        return '/api/user/' + this.user.get('id') + '/dran';
    },

    initialize() {
        this.user = dataChannel.request('logged:in:user');
    },

    addId(id, name) {
        const g = new Game({id: id});
        if (name) {
            g.set('name', name);
        }
        this.add(g);
    },
});
