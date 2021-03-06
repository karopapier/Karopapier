const Backbone = require('backbone');
const DranApp = require('../app/DranApp');
const GameApp = require('../app/GameApp');
const EditorApp = require('../app/EditorApp');

module.exports = Backbone.Router.extend({
    initialize(options) {
        options = options || {};
        if (!options.app) {
            console.error('No app for AppRouter');
            return false;
        }
        this.app = options.app;
        this.app.APPS = {};
    },
    routes: {
        '': 'showChat',
        'index.html': 'showChat',
        'dran.html': 'showDran',
        'editor.html': 'showEditor',
        'game.html?GID=:gameId': 'showGame',
        'game.html': 'showGame',
        'newshowmap.php?GID=:gameId': 'showGame',
        'game.html': 'defaultRoute',
        ':path': 'showStatic',
    },
    doDummy(info) {
        let a;
        if (info in this.app.APPS) {
            a = this.app.APPS[info];
        } else {
            a = new DummyApp({
                info,
            });
            a.start();
            this.app.APPS[info] = a;
        }
        this.app.layout.content.show(a.view, {preventDestroy: true});
    },
    showStatic(path) {
        this.doDummy(path);
        return;

        this.app.layout.content.show(new StaticView({
            path,
        }));
    },
    showEditor() {
        this.app.editorApp = new EditorApp({
            app: this.app,
        });
        this.app.layout.content.show(this.app.editorApp.layout);
    },
    showDran() {
        this.app.dranApp = new DranApp();
        this.app.layout.content.show(this.app.dranApp.view);
    },
    showGame(gameId) {
        this.app.gameApp = new GameApp({
            app: this.app,
            settings: this.app.Settings,
        });
        this.app.layout.content.show(this.app.gameApp.view);
    },
    defaultRoute() {
        this.navigate('index.html', {trigger: true});
    },
});

