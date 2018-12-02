const $ = require('jquery');
const Marionette = require('backbone.marionette');

const YOUTUBE_CACHE = {};

module.exports = Marionette.Object.extend({
    initialize() {
        this.funny = true;
        this.oldLink = true;

        this.replacements = [];
        this.replacements.push({
            r: '<a (.*?)</a>',
            f(a) {
                // real-link protector
                return a;
            },
            sw: 'i',
        });

        // Formatting
        this.replacements.push({
            r: '-:K',
            f: '<i>',
        });
        this.replacements.push({
            r: 'K:-',
            f: '</i>',
        });
        this.replacements.push({
            r: '-:F',
            f: '<b>',
        });
        this.replacements.push({
            r: 'F:-',
            f: '</b>',
        });
        this.replacements.push({
            r: '-:RED',
            f: '<span style="color: red">',
        });
        this.replacements.push({
            r: 'RED:-',
            f: '</span>',
        });

        if (this.funny) {
            // Eier
            /* Ostern vorbEI
                this.replacements.push({
                    r: "ei",
                    f: function() {
                        var i=Math.round(Math.random()*4)+1;
                        var ei = "ei" + i;
                        return ' <img src="//2.karopapier.de/images/eier/' + ei + '.png" alt="Ei" title="Ei" />';
                    },
                    sw: "i"
                });
                */

            // -:Pic
            this.replacements.push({
                r: '-:Pic src=(.*?) Pic:-',
                f(text) {
                    return '<img src="http://daumennagel.de/' + RegExp.$1 + '" />';
                    // return '<img src="' + RegExp.$1 + '" />';
                },
            });

            // nen
            this.replacements.push({
                r: '(^|\\s)nen(^|\\s|$)',
                f(text) {
                    return RegExp.$1 + 'einen' + RegExp.$2;
                },
            });

            // Nen
            this.replacements.push({
                r: '(^|\\s)Nen(^|\\s|$)',
                f() {
                    return RegExp.$1 + 'Einen' + RegExp.$2;
                },
            });

            // Thomas Anders
            this.replacements.push({
                r: '\\banders\\b',
                f() {
                    return ' <img style="opacity: .3" src="/images/anders.jpg" alt="anders" title="anders" />';
                },
                sw: 'i',
            });

            // The HOFF
            this.replacements.push({
                r: '\\bhoff\\b',
                f() {
                    return ' <img style="opacity: .3" src="/images/hoff.jpg"     alt="hoff" title="hoff" />';
                },
                sw: 'i',
            });
        }

        // GID
        this.replacements.push({
            r: '(?:http\\:\\/\\/www.karopapier.de\\/showmap.php\\?|http:\\/\\/2.karopapier.de\\/game.html\\?|\\b)GID[ =]([0-9]{3,6})\\b', // eslint-disable-line max-len
            f(all, gid) {
                // console.log("All", all);
                // console.log("GID", gid);
                $.getJSON('/api/game/' + gid + '/info.json', (gameInfo) => {
                    $('a.GidLink' + gid).text(gid + ' - ' + gameInfo.game.name);
                });
                if (this.oldLink) {
                    return '<a class="GidLink' + gid + '" href="//www.karopapier.de/showmap.php?GID=' + gid + '" target="_blank">' + gid + '</a>'; // eslint-disable-line max-len
                } else {
                    return '<a class="GidLink' + gid + '" href="//2.karopapier.de/game.html?GID=' + gid + '" target="_blank">' + gid + '</a>'; // eslint-disable-line max-len
                }
            },
            sw: 'i',
        });

        // Links
        this.replacements.push({
            r: '(?![^<]+>)((https?\\:\\/\\/|ftp\:\\/\\/)|(www\\.))(\\S+)(\\w{2,4})(:[0-9]+)?(\\/|\\/([\\w#!:.?+=&%@!\\-\\/]))?', // eslint-disable-line max-len
            f(url) {
                // console.log("URL MATCH", url);
                let className = '';
                let linktext = url;
                let linktitle = url;
                if (url.match('^https?:\/\/')) {
                    // linktext = linktext.replace(/^https?:\/\//i,'')
                    // linktext = linktext.replace(/^www./i,'')
                } else {
                    url = 'http://' + url;
                }

                // special handdling: youtube
                if (url.match('youtube.com/.*v=.*') || url.match('youtu.be/.*')) {
                    // console.log("Its a yt url", url);
                    let videoid = 0;
                    try {
                        videoid = url.split('?')[1].split('&').filter((part) => {
                            return part.substr(0, 2) == 'v=';
                        })[0].split('=')[1];
                    } catch (err) {
                        // console.log("Try yt");
                        videoid = url.split('tu\.be/')[1];
                    }
                    // console.log("Its a yt url", url, videoid);
                    className += ' yt_' + videoid;
                    const ytUrl = 'https://www.googleapis.com/youtube/v3/videos?id=' + videoid + '&key=AIzaSyBuMu8QDh49VqGJo4cSS4_9pTC9cqZwy98&part=snippet'; // eslint-disable-line max-len
                    if (videoid in YOUTUBE_CACHE) {
                        const snippet = YOUTUBE_CACHE[videoid];
                        linktext = '<img height="20" src="' + snippet.thumbnails.default.url + '" />' + snippet.title; // eslint-disable-line max-len
                        linktitle = snippet.description;
                    } else {
                        // console.log(ytUrl);
                        $.getJSON(ytUrl, (data) => {
                            const snippet = data.items[0].snippet;
                            YOUTUBE_CACHE[videoid] = snippet;
                            linktext = '<img height="20" src="' + snippet.thumbnails.default.url + '" />' + snippet.title; // eslint-disable-line max-len
                            $('a.yt_' + videoid).attr('title', snippet.description).html(linktext);
                        });
                    }
                } else if (url.match(/.*\.(jpg|gif|png)/i)) {
                    // console.log("Handling jpg url", url);
                    linktext = '<img src="' + url + '" height="20" />';
                } else {
                    // console.log("Handling default url", url, text);
                    if (url.match('^https?:\/\/')) {
                        linktext = linktext.replace(/^https?:\/\//i, '');
                        linktext = linktext.replace(/^www./i, '');
                    }
                }

                return '<a class="' + className + '" title="' + linktitle + '" target="_blank" rel="nofollow" href="' + url + '">' + linktext + '</a>'; // eslint-disable-line max-len
            },
            sw: 'i',
        });

        // Smilies
        this.replacements.push({
            r: ':([a-z]*?):',
            f(all, smil) {
                // console.log(smil);
                const img = document.createElement('img');
                img.src = '//www.karopapier.de/bilder/smilies/' + smil + '.gif';
                img.onload = () => {
                    // console.log("Ich lud");
                    $('.smiley.' + smil).replaceWith(img);
                };
                return '<span class="smiley ' + smil + '">' + all + '</span>';
            },
            sw: 'i',
        });

        this.replacements.push({
            r: 'img src="\\/images\\/smilies\\/(.*?).gif" alt=',
            f(all, smil) {
                // console.log(all, smil);
                return 'img src="//www.karopapier.de/bilder/smilies/' + RegExp.$1 + '.gif" alt=';
            },
            sw: 'i',
        });

        this.replacements.push({
            // r: '&lt;img src="\\/images\\/smilies\\/(.*?).gif" alt="(.*?)" title="(.*?)"&gt;',
            r: '&lt;img src=&quot;/images/smilies/(.*?).gif&quot; alt=&quot;.*?&quot; title=&quot;.*?&quot;&gt;',
            f(all, img) {
                return '<img src="/images/smilies/' + img + '.gif">';
            },
        });
    },

    linkify(text) {
        if (!text) return text;

        // console.log('Look at', text, 'in', this);
        const l = this.replacements.length;
        for (let i = 0; i < l; i++) {
            const rpl = this.replacements[i];
            const r = rpl.r; // regex string
            const f = rpl.f; // return function/value
            const sw = rpl.sw || ''; // switch
            // console.log(r, sw);

            let rx;
            if ('rx' in rpl) {
                rx = rpl.rx; // prepared regex
            } else {
                rx = new RegExp('^(.*?)(' + r + ')(.*?)$', sw);
                rpl.rx = rx;
            }
            // console.log(rx);
            const parts = rx.exec(text);
            if (parts) {
                // console.log('Match for', rx, parts);
                const before = parts[1];
                const matchingText = parts[2];
                const after = parts[parts.length - 1]; // letzter nach allen matchin brackets
                // console.log(1, before, 2, matchingText, 3, after);
                const textToReturn = this.linkify(before) + matchingText.replace(new RegExp(r, sw), f) + this.linkify(after); // eslint-disable-line max-len
                // console.info(textToReturn);
                return textToReturn;
            }
        }
        // console.log("No match, return text", text);
        // nothing matches?
        return text;
    },
});
