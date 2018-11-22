module.exports = Backbone.Marionette.ItemView.extend({
    'template': '#game-navi-template',
    'events': {
        'click .back': 'backGame',
        'click .next': 'nextGame',
        'click .smaller': 'smallerView',
        'click .bigger': 'biggerView',
    },

    'smallerView'() {
        let size = app.gameAppView.gameView.settings.get('size');
        app.gameAppView.gameView.settings.set({'size': size - 1});
    },

    'biggerView'() {
        let size = app.gameAppView.gameView.settings.get('size');
        app.gameAppView.gameView.settings.set({'size': size + 1});
    },

    'backGame'() {
        let oldId = parseInt(app.gameAppView.model.get('id'));
        app.router.navigate('game/' + (oldId - 1), {trigger: true});
    },
    'nextGame'() {
        let oldId = parseInt(app.gameAppView.model.get('id'));
        app.router.navigate('game/' + (oldId + 1), {trigger: true});
    },
});

