const Backbone = require('backbone');
const Contact = require('../model/Contact');
module.exports = Backbone.Collection.extend({
    model: Contact,
    url: '/api/contacts'
});

