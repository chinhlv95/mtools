$(function () {
    'use strict';
    var btn_add_member = '#btnAddMember',
        addMemberModal = '#addMemberPopup',
        modal = '.modal';
    
    $(window).on('click',function(e){
        if(e.target == modal){
            $(modal).hide();
        }
    });
    //open popup
    $(btn_add_member).on('click',function(){
        $(addMemberModal).show();
    });
  //close popup
    $('.close-popup-add').on('click',function(){
        $(modal).hide();
    });
    $('.close-popup-edit').on('click',function(){
        $(modal).hide();
    });
    //close popup
    $('.btn-cancel-popup').on('click',function(){
        $(modal).hide();
    });
    $('.btn-close-popup-edit').on('click',function(){
        $(modal).hide();
    });
});