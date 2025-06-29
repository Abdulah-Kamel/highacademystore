$.toast({
    heading: 'Positioning',
    icon: 'info', // info success warning error
    text: 'Use the predefined ones, or specify a custom position object.',
    position: 'top-right',// bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values
    stack: 5,
    allowToastClose:true,
    showHideTransition: 'slide', // fade|plain|slide
    hideAfter:5000, // false
    textAlign:"left",
    loader:true,
    loaderBg: '#9ec600',  // Background color of the toast loader
    beforeShow: function () {}, // will be triggered before the toast is shown
    afterShown: function () {}, // will be triggered after the toat has been shown
    beforeHide: function () {}, // will be triggered before the toast gets hidden
    afterHidden: function () {},
    //bgColor: '#FF1356',
    //textColor: 'white'
    // class: 'larger-font'
})
