console.warn('THOU SHALST NOT USE ME ANYMORE');
require('../polyfills.js');
const Backbone = require('backbone');
const User = require('../model/User');

module.exports = Backbone.Model.extend(/** @lends UserFactory.prototype */ {
    /**
     * Provides central access to cached user instances.
     * Users with same ID will share the same instance
     *
     * @class UserFactory
     * @constructor Userfactory
     * @param options
     */
    initialize: function(options) {
        options = options || {};

        this.userCache = new Backbone.Collection();
        this.uid = 0;
        this.login = new User();
    },
    getUser: function(data) {
        // console.log("Get user",data);
        let user = this.userCache.get(data);
        // console.log("Got user",user);
        if (user) {
            console.info('UserFactory HIT', user.get('id'));
            user.set(data);
            return user;
        }

        if (typeof data === 'number') {
            data = {id: data};
        }
        user = new User(data);
        console.warn('Userfactory NEW', user.get('id'));
        user.set('self', (user.get('id') === this.uid));
        this.userCache.add(user);
        return user;
    },

    setLogin: function(user) {
        this.stopListening(this.login);
        this.login = user;
        this.listenTo(this.login, 'change id', this.updateSelf.bind(this));
        this.updateSelf();
    },
    updateSelf: function() {
        let me = this;
        this.uid = this.login.get('id');
        this.userCache.each(function(u) {
            u.set('self', (u.get('id') === me.uid));
        });
    },
});
