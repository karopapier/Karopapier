const helpers = {};

const shortMonths = [
    'Jan',
    'Feb',
    'Mär',
    'Apr',
    'Mai',
    'Jun',
    'Jul',
    'Aug',
    'Sep',
    'Okt',
    'Nov',
    'Dez',
];

const shortWeekdays = [
    'So',
    'Mo',
    'Di',
    'Mi',
    'Do',
    'Fr',
    'Sa',
];

helpers.getLocalShortMonth = function(d) {
    const idx = d.getMonth();
    return shortMonths[idx];
};

helpers.getLocalShortWeekday = function(d) {
    const idx = d.getDay();
    return shortWeekdays[idx];
};

helpers.getCurrentYmd = function(date) {
    if (!date) {
        date = new Date();
    }

    const Y = date.getFullYear();
    let m = date.getMonth() + 1;
    let d = date.getDate();
    if (m < 10) m = '0' + m;
    if (d < 10) d = '0' + d;

    return Y + '-' + m + '-' + d;
};

module.exports = helpers;
