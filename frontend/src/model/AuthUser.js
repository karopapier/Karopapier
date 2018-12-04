const User = require('./User');
module.exports = User.extend({
    url: '/api/user/check',

    isLoggedIn() {
        return this.get('id') > 0;
    },
});

