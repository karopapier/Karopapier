const User = require('../module/data-manager/model/User');
module.exports = User.extend({
    defaults: {
        'gamesort': 'blocktime',
    },

    url: '/api/user/check',

    isLoggedIn() {
        return this.get('id') > 0;
    },
});

