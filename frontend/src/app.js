const $ = require('jquery');
let MessagingApp = require('./app/MessagingApp');
$(document).ready(function() {
    window.app = new MessagingApp();

    $.when(
        app.contacts.fetch(),
        app.messages.fetch()
    ).done(function() {
        app.start();
    });
});
