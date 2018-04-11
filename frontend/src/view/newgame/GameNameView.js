const $ = require('jquery');
const Marionette = require('backbone.marionette');
module.exports = Marionette.View.extend({
    template: require('../../../templates/newgame/newgameName.html'),

    ui: {
        name: 'input',
    },

    events: {
        'click .newgame-random-wiki': 'randWiki',
        'click .newgame-random-chefkoch': 'randKoch',
        'click .newgame-random-volkswurst': 'randWurst',
        'click .newgame-random-managerator': 'randMana',
        'click .newgame-random-sprichwort': 'randSprichwort',
    },

    randWiki() {
        this.getUI('name').val('...frage....Wikipedia.......');
        $.getJSON(
            '//de.wikipedia.org/w/api.php?action=query&list=random&rnlimit=1&rnnamespace=0&format=json&callback=?',
            (data) => {
                this.getUI('name').val(data.query.random[0].title);
            });
    },

    randWurst() {
        this.getUI('name').val('...frage...die...Volkswurst.....');
        $.getJSON('//www.karopapier.de/volkswurst.php?callback=?', (data) => {
            this.getUI('name').val(data);
        });
    },

    randKoch() {
        this.getUI('name').val('...frage...den...Chefkoch.....');
        $.getJSON('//www.karopapier.de/chefkoch.php?callback=?', (data) => {
            this.getUI('name').val(data);
        });
    },

    randMana() {
        this.getUI('name').val('...frage...den...Managerator.....');
        $.getJSON('//www.managerator.de/json.php?word=sentence&callback=?', (data) => {
            this.getUI('name').val(data);
        });
    },

    randSprichwort() {
        this.getUI('name').val('...frage...den...Sprichwortrekombinator.....');
        $.getJSON('//www.karopapier.de/sprichwort.php?callback=?', (data) => {
            this.getUI('name').val(data);
        });
    },
});
