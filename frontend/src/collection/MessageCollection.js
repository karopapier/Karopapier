var Backbone = require('backbone');
var Message = require("../model/Message");
module.exports = Backbone.Collection.extend({
    model: Message,
    comparator: "ts",
    url: "/api/messages"
});

