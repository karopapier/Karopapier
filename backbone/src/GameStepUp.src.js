const $ = require('jquery');
const Backbone = require('backbone');
require('babel-polyfill');
const Game = require('./model/Game');
const MapViewSettings = require('./model/map/MapViewSettings');
const MoveMessagesView = require('./view/game/MoveMessagesView');
const LastMoveMessageView = require('./view/game/LastMoveMessageView');
const GameInfoView = require('./view/game/GameInfoView');
const GameTitleView = require('./view/game/GameTitleView');
const MapRenderView = require('./view/map/MapRenderView');
const StatusView = require('./view/game/StatusView');
const PlayerTableView = require('./view/game/PlayerTableView');
const MapPlayersMoves = require('./view/map/MapPlayersMoves');
const PossiblesView = require('./view/game/PossiblesView');
const DranGameCollection = require('./collection/DranGameCollection');
const GameAppRouter = require('./router/GameAppRouter');
const Move = require('./model/Move');

const KaropapierApp = require('./app/KaropapierApp');
window.Karopapier = new KaropapierApp({
    realtimeHost: 'turted.karopapier.de',
});

$(document).ready(() => {
    console.log('Doc ready, start Karopapier app');
    Karopapier.start();
    console.log('App started');
});

Karopapier.User.on('change:id', () => {
    $('#username').text(Karopapier.User.get('login'));
});

window.game = new Game();
window.mvs = new MapViewSettings();

const mmv = new MoveMessagesView({
    el: '#moveMessages',
    collection: game.get('moveMessages'),
});
mmv.render();

const lmmv = new LastMoveMessageView({
    el: '#lastMoveMessages',
    collection: game.get('moveMessages'),
});

new GameInfoView({
    model: game,
    el: '#gameInfo',
});

new GameTitleView({
    el: '#gameTitle',
    model: game,
});

const renderView = new MapRenderView({
    el: '#mapRenderView',
    model: game.map,
    settings: mvs,
});

game.on('change:completed', () => {
    if (!game.get('completed')) return false;
    renderView.settings.set('cpsActive', game.get('withCheckpoints'));
    const dranId = game.get('dranId');
    if (dranId !== 26) {
        const cpsVisited = game.get('players').get(game.get('dranId')).get('checkedCps');
        renderView.settings.set('cpsVisited', cpsVisited);
    }
});

new StatusView({
    model: game,
    el: '#statusinfo',
});

new PlayerTableView({
    collection: game.get('players'),
    el: '#playerTable',
}).render();

const mpm = new MapPlayersMoves({
    model: game,
    collection: game.get('players'),
    settings: mvs,
    el: '#mapPlayerMoves',
});

const possView = new PossiblesView({
    el: '#mapImage',
    game,
    mapView: renderView,
});

possView.on('game:player:move', (playerId, mo) => {
    const testmode = $('#testmode').is(':checked');
    if (testmode) {
        const player = game.get('players').get(playerId);
        const move = new Move(mo.toMove());
        move.set('t', new Date());
        move.set('test', true);
        player.moves.add(move);
        game.updatePossibles();
        mpm.render();
    } else {
        // build move url
        let moveUrl = '/move.php?GID=' + game.get('id');
        const m = mo.toMove();
        if (mo.get('vector').getLength() === 0) {
            // http://www.karopapier.de/move.php?GID=84078&startx=8&starty=29
            moveUrl += '&startx=' + m.x + '&starty=' + m.y;
        } else {
            // http://www.karopapier.de/move.php?GID=83790&xpos=76&ypos=28&xvec=-2&yvec=2
            moveUrl += '&xpos=' + m.x + '&ypos=' + m.y + '&xvec=' + m.xv + '&yvec=' + m.yv;
        }

        const moveMsg = $('#movemessage').val();
        if (moveMsg !== '') {
            moveUrl += '&movemessage=' + moveMsg;
            $('#movemessageDisplay').hide();
            $('#movemessage').show().val('');
        }

        // console.log("Send move");
        const movedGID = game.get('id');
        // console.warn("I just moved", movedGID);
        myTextGet(moveUrl, (text) => {
            // console.log("Parse move response");
            parseMoveResponse(text, movedGID);
        });
        dranQueue.remove(game.get('id'));
        // console.log("Now HIDING");
        $('#mapImage').hide();
        game.set({'completed': false, 'moved': true});
        // console.log("Done with this game");
    }
});

function parseMoveResponse(text, movedGID) {
    // indexOf Danke ==ok
    if ((text.indexOf('Danke.') >= 0) || (text.indexOf('Spiel beendet') >= 0)) {
        // <B>Didi</B> kommt als n&auml;chstes dran
        const hits = text.match(/<B>(.*?)<\/B> kommt als n/);
        let nextPlayer = 'Unknown';
        if (hits) {
            if (hits.length > 1) {
                nextPlayer = hits[1];
            }
        }
        if (nextPlayer == Karopapier.User.get('login')) {
            // console.info("NOMMAL DRAN");
            dranQueue.addId(game.get('id'));
        }

        // console.log("check listed next games and add them to queue as well, excluding moved one");
        const gids = text.match(/GID=(\d*)/g);
        let dranQueueEmpty = (dranQueue.length == 0);
        if (gids.length > 0) {
            for (let i = 0; i < gids.length; i++) {
                const s = gids[i].split('=');
                if (s) {
                    const gid = s[1];
                    // console.log("Compare found", gid, "with", movedGID);
                    if (gid != movedGID) {
                        // console.log(gid, "!=", movedGID, ", so add it to queue");
                        dranQueueEmpty = false;
                        dranQueue.addId(gid);
                    }
                }
            }
        }

        // last game is added and immediately removed when loading
        // so we might have an empty queue but are currently loading the new game
        // thus the "dranQueueEmpty" workaround
        // console.log("Done adding, queue length now", dranQueue.length);

        // if, after parsing, still no games in queue... Nixblocker, goto chat
        if (dranQueueEmpty) {
            window.location.href = '/chat.html';
        }
    } else {
        alert('KEIN DANKE!!! Da hat wohl was nicht gepasst');
        console.log(text);
    }
}

function myTextGet(url, cb, errcb) {
    const request = new XMLHttpRequest();
    request.withCredentials = true;
    request.open('GET', url, true);
    request.onload = function() {
        if (request.status >= 200 && request.status < 400) {
            // Success!
            // console.log(request.responseText);
            cb(request.responseText);
            // console.log("Success: ",request.responseText.indexOf("Danke.") >=0);
        } else {
            // We reached our target server, but it returned an error
            cb(request.responseText);
            // console.log("doof",request);
        }
    };

    request.onerror = function() {
        // There was a connection error of some sort
    };

    request.send();
}

const checkTestmode = function() {
    if ($('#testmode').prop('checked')) {
        $('#mapImage').addClass('testmode');
        return;
    }

    $('#mapImage').removeClass('testmode');
    const dranId = game.get('dranId');
    const dranPlayer = game.get('players').get(dranId);
    console.log(game.get('players'));
    // const dranMoves = myPlayer.get("moves"); #FIXME
    const dranMoves = dranPlayer.moves;

    const noTestMoves = dranMoves.where({'test': false});
    dranPlayer.moves.set(noTestMoves);
    mpm.render();
    game.updatePossibles();
};

$('#testmode').click(checkTestmode);
// Make sure to start with testmode - Looking at YOU, Firefox!!!
$('#testmode').prop('checked', true);
checkTestmode();

$('.drawMoveLimit').click((e) => {
    const l = $(e.currentTarget).data('limit');
    mvs.set('drawLimit', l);
});

$('#moveMsgForm').submit((e) => {
    const moveMsg = $('#movemessage').val();
    const w = $('#movemessage').width();
    if (moveMsg !== '') {
        $('#movemessageDisplay').text(moveMsg).css({'display': 'inline-block', 'width': w});
        $('#movemessage').hide();
    }
    e.preventDefault();
});

const dranQueue = new DranGameCollection();

// inital load via reset
dranQueue.listenTo(Karopapier.User, 'change:id', dranQueue.fetch.bind(dranQueue, {reset: true}));
// dranQueue.fetch({reset: true});

const nextGame = new Game();

// ///////////////////////////////////////////////////////////////////////////
// EVENTS
// ///////////////////////////////////////////////////////////////////////////

game.on('change:completed', () => {
    // console.log("Completed", game.get("completed"));
    if (!(game.get('completed'))) return false;

    $('#mapImage').show();

    // set limit for "LastMoveMessages"
    const dranId = game.get('dranId');
    let ts = false;
    if (dranId) {
        const p = game.get('players').get(dranId);
        if (p) {
            const lastmove = p.moves.last();
            if (lastmove) {
                ts = new Date(lastmove.get('t'));
            }
        }
    }

    // console.log("Setting lastmove message filter to ", ts);
    lmmv.settings.set('timestamp', ts);
});

game.on('change:moved', () => {
    // console.log("Game changed moved to ", game.get("moved"));
    if (game.get('moved')) {
        checkNextGame();
    }
});

nextGame.on('change:completed', () => {
    if (nextGame.get('completed')) {
        // console.log("Next game is completed. Wanna have it?");
        checkNextGame();
    }
});

dranQueue.on('reset', (q, e) => {
    // console.info("DranQueue INITIAL reset");
    // make sure to remove currently showing game from queue
    checkPreload();
});

dranQueue.on('add', (g, q, e) => {
    // console.info("DranQueue add", g.get("id"));
    checkNextGame();
    checkPreload();
});

dranQueue.on('remove', (g, q, e) => {
    // console.info("DranQueue remove", g.get("id"));
    checkPreload();
});

// ///////////////////////////////////////////////////////////////////////////
const checkPreload = function() {
    // console.log("Preparing buffer");
    if (dranQueue.length > 0) {
        // console.log("DQ len", dranQueue.length);
        const nextId = dranQueue.at(0).get('id');
        // console.log("Next ID", nextId);

        if ((nextId == game.get('id')) && (!game.get('moved'))) {
            // console.log("next == current, kicking from queue");
            dranQueue.shift();
            return false;
            // checkPreload(); //will be triggered by "remove"
        }

        if (nextGame.get('id') === 0) {
            // console.log("Trigger preload of", nextId);
            nextGame.set('id', nextId);
            setTimeout(() => {
                nextGame.load(nextId);
            }, 50);
        } else {
            // console.log("Preload already in progress");
        }
    } else {
        // console.log("DQ empty");
    }
};

const gar = new GameAppRouter();
const checkNextGame = function() {
    // console.log("checking next game:");
    // console.log("Game moved:", game.get("moved"));
    // console.log("NextGame completed:", nextGame.get("completed"));
    // console.log("NextGame: ", nextGame);

    if (((game.get('id') == 0) || (game.get('moved'))) && nextGame.get('completed')) {
        // console.log("Setting game from next");
        nextGame.set('moved', false);
        game.setFrom(nextGame);
        gar.navigate(window.location.pathname.substr(1) + '?GID=' + nextGame.get('id'));
        // console.log("Now showing");
        $('#mapImage').show();
        nextGame.set({id: 0, completed: false}, {silent: true});
        checkPreload();
    } else {
        if (nextGame.get('id') === 0) {
            // console.log("Nixblocker");
        } else {
            // console.log("not completed yet or not moved yet");
        }
    }
};


Backbone.history.start({
    pushState: true,
});

// console.info("Stepup done");
