const $ = require('jquery');
const KaropapierApp = require('./app/KaropapierApp');

// GLOBAL LEAKAGE IS INTENDED!!!! Sorry...
window.Karopapier = new KaropapierApp({
    realtimeHost: 'turted.karopapier.de',
});

// TODO: need to get rid of that
Karopapier.vent.on('logout', () => {
    console.log('Logging out');
    Karopapier.User.set('id', 0);
    console.log(Karopapier.User);
});

Karopapier.vent.on('login', () => {
    console.log('Logging in');
    // TODO Get rid of this AND jQuery require
    $.post('//www.karopapier.de/api/user/login.json', {
        login,
        'password': pass,
    }, (data) => {
        console.log(data);
        Karopapier.User.set(data);
    });
});

$(document).ready(() => {
    console.log('Doc ready, start Karopapier app');
    Karopapier.start();
    console.log('App started');
});
