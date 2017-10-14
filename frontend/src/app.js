var $ = require('jquery');
var MessagingApp = require('./app/MessagingApp');
$(document).ready(function() {
    console.log("In ready");
    window.app = new MessagingApp();

    $.when(
        app.contacts.fetch(),
        app.messages.fetch()
    ).done(function() {
        app.start();
    });
});
