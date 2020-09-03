
/**
 * A simple method to convert a string to it's "title case" representation.
 *
 * title case => Title Case
 * tITLE CAse => Title Case
 *
 * @returns {string}
 */
String.prototype.title_case = function () {
    this.toLowerCase();
    var components = this.split(' ');
    return components.map(function (component) {
        return component.charAt(0).toUpperCase() + component.substr(1).toLowerCase();
    }).join(' ');
};