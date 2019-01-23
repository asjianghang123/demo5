/**
 * Created by efjlmmo on 2016/7/11.
 */
var toogle = function (route) {
    var parents = $('a[href="' + route + '"]').parentsUntil('.sidebar-menu', "li");
    var child;
    for (var index = 0, length = parents.length; index < length; index++) {
        if ($(parents[index]).hasClass("treeview")) {
            $(parents[index]).toggleClass("active");
        } else {
            $(parents[index]).addClass("active");
        }
    }
};