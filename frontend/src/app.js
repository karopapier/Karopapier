const waiter = setInterval(() => {
    console.log('Check libs');
    if (window.libsloaded) {
        clearInterval(waiter);
        bootstrap();
    }
}, 1000);

function bootstrap() {
    const KaroApp = require('./app/KaroApp');
    const app = new KaroApp({
        turtedHost: window.turtedHost,
    });
    app.authUser.once('sync', () => {
        app.start();
    });
}
