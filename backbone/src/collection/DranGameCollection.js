var _ = require('underscore');
var Backbone = require('backbone');
var Game = require('../model/Game');
module.exports = Backbone.Collection.extend({
    model: Game,
    url: function() {
        return APIHOST + "/api/user/" + Karopapier.User.get("id") + "/dran.json?callback=?";
    },
    initialize: function(options) {
        _.bindAll(this, "addId", "url");
    },
    addId: function(id, name) {
        var g = new Game({id: id});
        if (name) {
            g.set("name", name);
        }
        this.add(g);
    },
    parse: function(data) {
        return data.games;
    }
});