/**
 * Display a listing of the resource.
 *
 * @return Toster
 */
function successToster(heading,text){
    $.toast({
        heading: heading,
        icon: 'success', // info success warning error
        text: text,
        position: 'top-right',// bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values
        stack: false,
        allowToastClose:true,
        showHideTransition: 'slide', // fade|plain|slide
        hideAfter:5000, // false
        textAlign:"left",
        loader:true,
        loaderBg: '#ffffff',  // Background color of the toast loader
    })
}

function validToster(heading,text){
    $.toast({
        heading: heading,
        icon: 'error', // info success warning error
        text: text,
        position: 'top-center',// bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values
        stack: false,
        allowToastClose:true,
        showHideTransition: 'plain', // fade|plain|slide
        hideAfter:10000, // false
        textAlign:"center",
        loader:true,
        loaderBg: '#ffffff',  // Background color of the toast loader
    })
}

function errorToster(heading,text){
    $.toast({
        heading: heading,
       // icon: 'error', // info success warning error
        text: text,
        position: 'top-right',// bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values
        stack: false,
        allowToastClose:true,
        showHideTransition: 'plain', // fade|plain|slide
        hideAfter:10000, // false
        textAlign:"left",
        loader:true,
        loaderBg: '#ffffff',  // Background color of the toast loader
    })
}

function warningToster(heading,text){
    $.toast({
        heading: heading,
        icon: 'warning', // info success warning error
        text: text,
        position: 'top-right',// bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values
        stack: false,
        allowToastClose:true,
        showHideTransition: 'plain', // fade|plain|slide
        hideAfter:10000, // false
        textAlign:"left",
        loader:true,
        loaderBg: '#ffffff',  // Background color of the toast loader
    })
}

/**
 * Display a listing of the resource.
 *
 * @return dataTable
 */

$(".password-meter-input").on("keyup", function () {
    var e = 0,
        t = $(this).val(),
        c = new RegExp("[A-Z]"),
        o = new RegExp("[a-z]"),
        n = new RegExp("[0-9]"),
        r = new RegExp("^(?=.*?[#?!@$%^&*-]).{1,}$");
    7 < t.length && e++,
    0 < t.length && t.match(c) && e++,
    0 < t.length && t.match(o) && e++,
    0 < t.length && t.match(n) && e++,
    0 < t.length && t.match(r) && e++,
     $(".progress-bar-bar")[0].style.width = 20 * e + "%"
})
