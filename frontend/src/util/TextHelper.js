// Polyfills
const helpers = {};
// Make sure we trim BOM and NBSP
const rtrim = /^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g;

helpers.trim = function(str) {
    return str.replace(rtrim, '');
};

helpers.startsWith = function(searchString, position) {
    position = position || 0;
    return this.indexOf(searchString, position) === position;
};

helpers.truncate = function(str, maxlength) {
    if (str.length < maxlength) {
        return str;
    }

    return str.substring(0, maxlength) + '...';
};

helpers.repeat = function(str, n) {
    n = n || 1;
    return new Array(n + 1).join(str);
};

module.exports = helpers;
