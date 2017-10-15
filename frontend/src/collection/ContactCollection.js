var Backbone = require('backbone');
var Contact = require('../model/Contact');
module.exports = Backbone.Collection.extend({
    model: Contact,
    url: "/api/contacts"
});

