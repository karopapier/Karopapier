const User = require('../module/data-manager/model/User');
module.exports = User.extend({
    url: '/api/user/check',

    isLoggedIn() {
        return this.get('id') > 0;
    },
});

