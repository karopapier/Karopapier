const BaseModel = require('./BaseModel');

module.exports = BaseModel.extend({
    defaults: {
        id: 0,
        login: '',
        activeGames: 0,
        dran: 0,
        chat: false, // currently in chat
        uc: 0,
    },

    incDran() {
        const old = this.get('dran');
        this.set('dran', old + 1);
    },

    decDran() {
        const old = this.get('dran');
        if (old > 0) {
            this.set('dran', old - 1);
        }
    },
});

