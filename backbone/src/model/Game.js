const _ = require('underscore');
const Backbone = require('backbone');
const MoveMessageCollection = require('../collection/MoveMessageCollection');
const Map = require('./map/Map');
const PlayerCollection = require('../collection/PlayerCollection');
const MotionCollection = require('../collection/MotionCollection');
const Motion = require('./Motion');
const Vector = require('./Vector');

module.exports = Backbone.Model.extend(/** @lends Game.prototype */ {
    defaults: {
        id: 0,
        completed: false,
        loading: false,
    },
    /**
     * @constructor Game
     * @class Game
     * @param options map
     */
    initialize: function(options) {
        options = options || {};
        _.bindAll(this, 'parse', 'load', 'updatePossibles');
        if (options.map) {
            this.map = options.map;
            if (typeof this.map === 'number') {
                this.map = new Map({
                    id: this.map,
                });
            }
        } else {
            this.map = new Map();
        }
        this.set('moveMessages', new MoveMessageCollection());
        // pass the MoveMessage collection into it to have the messages ready in one go when walking the moves
        this.set('players', new PlayerCollection());
        this.listenTo(this.get('players'), 'reset', this.get('moveMessages').updateFromPlayers);
        this.possibles = new MotionCollection();
        this.listenTo(this, 'change:completed', this.updatePossibles);
        this.listenTo(this.get('players'), 'movechange', function() {
            // console.log("movechange");
            this.updatePossibles();
        });
    },

    url: function() {
        return '/api/game/' + this.get('id') + '/details.json';
    },

    parse: function(data) {
        // console.log("PARSE");
        // make sure data is matching current gameId (delayed responses get dropped)
        if (this.get('id') !== 0) {
            // check if this is a details.json
            if (data.game) {
                // console.log("DETAIL PARSE");
                if (data.game.id == this.id) {
                    // pass checkpoint info to map as "cpsActive" // map has cps attr as well, array of avail cps
                    this.map.set({'cpsActive': data.game.cps}, {silent: true});
                    this.map.set(data.map);
                    // console.log("RESET PLAYERS NOW");
                    this.get('players').reset(data.players, {parse: true});
                    data.game.completed = true;
                    data.game.loading = false;
                    // console.log("RETURN DATA NOW");
                    return data.game;
                } else {
                    console.warn('Dropped response for ' + data.game.id);
                }
            }
        }
        return data;
    },

    load: function(id) {
        let hasId = this.get('id');

        // if not ID already set or passed, return
        if (!id && !hasId) return false;
        if (hasId > 0) id = hasId;

        // if already loading, return
        // @TODO: consider timeout of "loading"
        console.log('Game', id, 'is loading:', this.get('loading'));
        if (this.get('loading')) return false;
        // silently set the id, events trigger after data is here
        // this.set({"id": id, completed: false}, {silent: true});
        this.set({'id': id, 'completed': false, 'loading': true});
        console.info('Fetching game details for ' + id);
        this.fetch();
    },

    updatePossibles: function() {
        // console.warn("Start Recalc possibles for", this.get("id"));
        if (!(this.get('completed'))) return false;
        if (this.get('moved')) return false;
        if (this.get('finished')) {
            this.possibles.reset([]);
            return true;
        }
        // console.warn("Really DO recalc possibles for", this.get("id"));

        let dranId = this.get('dranId');
        if (this.get('players').length < 1) return false;
        let currentPlayer = this.get('players').get(dranId);
        if (!currentPlayer) return false;
        let movesCount = currentPlayer.moves.length;

        // FIXME
        let theoreticals;

        // if no moves but dran and active, return starties
        if ((movesCount === 0) && (currentPlayer.get('status') == 'ok')) {
            theoreticals = this.map.getStartPositions().map(function(e) {
                let v = new Vector({x: 0, y: 0});
                let mo = new Motion({
                    position: e,
                    vector: v,
                });
                mo.set('isStart', true);
                return mo;
            });
        } else {
            let lastmove = currentPlayer.getLastMove();
            let mo = lastmove.getMotion();
            // get theoretic motions
            // reduce possibles with map
            theoreticals = mo.getPossibles();
            theoreticals = this.map.verifiedMotions(theoreticals);
        }

        // only for GID > 75000 limit to those that already moved
        let occupiedPositions = this.get('players').getOccupiedPositions((this.get('id') >= 75000));
        let occupiedPositionStrings = occupiedPositions.map(function(e) {
            return e.toString();
        });

        let possibles = [];
        for (let i = 0; i < theoreticals.length; i++) {
            let possible = theoreticals[i];
            if (occupiedPositionStrings.indexOf(possible.toKeyString()) < 0) {
                possibles.push(possible);
            }
        }
        this.possibles.reset(possibles);
    },
    /**
     * Set all nested parameters from other games data, keeping references intact
     * @param othergame
     */
    setFrom: function(othergame) {
        // console.warn("START SETTING FROM OTHER GAME");
        this.set('completed', false);
        othergame.set('completed', false);
        let attribsToSet = {};
        _.each(othergame.attributes, function(att, i) {
            if (typeof att !== 'object') {
                // console.log("Setting ", i, "to", att);
                attribsToSet[i] = att;
            }
        });
        this.set(attribsToSet);
        this.map.set(othergame.map.toJSON());
        // console.log(othergame.get("players").toJSON());
        this.get('players').reset(othergame.get('players').toJSON(), {parse: true});
        this.updatePossibles();
        // now set completed, really AT THE END
        this.set('completed', true);
        // console.warn("FINISHED SETTING FROM OTHER GAME");
    },
});
