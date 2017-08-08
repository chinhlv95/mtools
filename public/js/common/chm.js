$('.closeMessage').click(function(){
    $('.alert-danger').addClass('hide');
});


$(function () {
    'use strict';
    var chm_elm = '#open_chm',
        window_popup = '',
        root = '',
        toc_tree = '';
    $(chm_elm).on('click',function(){
        root = $(this).attr('root');
        window_popup = window.open(root+"chm_manual/WebHelp/index.htm", 
                                   "Measurement Helper", 
                                   "width=1024,height=768,resizable=yes,scrollbars=yes,titlebar=yes");
    });
});

$(function () {
    'use strict';
    var chm_elm_jp = '#open_chm_jp',
        window_popup = '',
        root = '',
        toc_tree = '';
    $(chm_elm_jp).on('click',function(){
        root = $(this).attr('root');
        window_popup = window.open(root+"chm_manual_jp/WebHelp/index.htm", 
                                   "Measurement Helper",
                                   "width=1024,height=768,resizable=yes,scrollbars=yes,titlebar=yes");
    });
});
