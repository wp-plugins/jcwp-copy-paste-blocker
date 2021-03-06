jQuery.fn.jccopyblock = function(a) {
    a = jQuery.extend({
        blockRightClick: false,
        blockDocTextSelection: false,
        useCSS: false,
        blockPageSave: false,
        alertUser: false,
        alertMessage: "Sorry! Content copy is not allowed?",
        callback: function() {}
    }, a);
    if (a.blockRightClick) {
        jQuery(document).contextmenu(function(b) {
            if (a.alertUser && a.alertMessage.length > 0) {
                alert(a.alertMessage)
            }
            b.preventDefault();
            return false
        })
    }
    if (a.blockDocTextSelection && !a.useCSS) {
        jQuery(document)[0].onselectstart = function(b) {
            if (a.alertUser && a.alertMessage.length > 0) {
                alert(a.alertMessage)
            }
            b.preventDefault();
            return false
        }
    } else if (a.blockDocTextSelection && a.useCSS) {
        jQuery("html,body").css({
            '-moz-user-select':'-moz-none',
            '-moz-user-select':'none',
            '-o-user-select':'none',
            '-khtml-user-select':'none',
            '-webkit-user-select':'none',
            '-ms-user-select':'none',
            'user-select':'none'
        }).bind('selectstart', function(){ return false; });
    }
    jQuery('body,html').keydown(function(b) {
        if (a.blockPageSave && b.ctrlKey && (b.which == 83 || b.which == 115 || b.which == 97 || b.which == 65 || b.which == 67 || b.which == 99)) {
            if (a.alertUser && a.alertMessage.length > 0) {
                alert(a.alertMessage)
            }
            b.preventDefault();
            return false
        }
    })
}