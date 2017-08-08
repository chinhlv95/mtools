var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'),
    checkedData = [];
$(function () {
    'use strict';
    
    var btnAddMember = '#btnAddMemberPopup',
        addMemberPopup = '#addMemberPopup',
        frmAddMember = '#frmAddNewMember',
        searchAjaxInput = '#autocomplete-ajax',
        paging = '.paging',
        btnOpenModal = '#btnAddMember',
        membersList = '#membersList',
        btnClosePopup = '.close-popup-add',
        oldData = '',
        searchMember = '#searchMember',
        projectId = $('#project_id').val(),
        selected_area = '.selected_area';
    
    if($(searchAjaxInput).length > 0){
        $(searchAjaxInput).autocomplete({
            serviceUrl: '../emails',
            dataType: 'json',
            type: 'GET',
            width: 300,
            onSelect: function (suggestion) {
                $('#autocomplete-ajax').val(suggestion.email);
            }
        });
    }
    
    $(frmAddMember).on('keypress',function(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
      });
    
    //handler keep checked item
    /*if($(membersList).length > 0){
        $(membersList).on('change','input[type=checkbox]',function(){
            if($(this).is(':checked')){
                checkedData.push(parseInt($(this).val()));
            }
            if($(this).is(':not(:checked)')){
                var i = checkedData.indexOf(parseInt($(this).val()));
                if(i != -1){
                    checkedData.splice(i,1);
                }
            }
        });
    }*/
    //handler append member to selected area
    if($(membersList).length > 0){
        $(membersList).on('change','input[type=checkbox]',function(){
            if($(this).is(':checked')){
                if($(selected_area).find(('input[value='+$(this).val()+']')).length == 0){
                    $(selected_area).append($('<div>')
                                             .addClass('col-lg-4 vert-offset-top-1')
                                             .html($(this).parent().parent().html())
                                            ).find(('input[value='+$(this).val()+']')).attr('checked','checked');
                }
            }
            if($(this).is(':not(:checked)')){
                $(selected_area).find(('input[value='+$(this).val()+']')).parent().parent().remove();
            }
        });
        $(selected_area).on('change','input[type=checkbox]',function(){
            if($(this).is(':not(:checked)')){
                $(membersList).find(('input[value='+$(this).val()+']')).prop('checked',false);
                $(this).parent().parent().remove();
            }
        });
    }
    //handler submit form
    if($(btnAddMember).length > 0){
        $(btnAddMember).on('click',function(e){
            var self = $(this);
            if($('input[type=checkbox]:checked').not(':disabled').length === 0){
                $('.error-message').find('strong').html('').append('You have not choosen member');
                e.preventDefault();
            }else if($('input[type=radio]:checked').length === 0){
                $('.error-message').find('strong').html('').append('You have not choosen role');
                e.preventDefault();
            }
            else{
                $(frmAddMember).submit();
                $(self).attr('disabled',true);
            }
        });
    }
    //handler save motonodata
    if($(btnOpenModal).length > 0){
        $(btnOpenModal).on('click',function(e){
            e.preventDefault();
            oldData = $(membersList).contents();
            if($('.pagination').is(':not(:visible)')){
                $('.pagination').show();
            }
        });
    }
    //handler paging ajax
    if($(paging).length > 0){
        $(addMemberPopup).on('click',paging,function(e){
            e.preventDefault();
            var pageTo = $(this).attr('data-page');
                getMemberFromJson('GET',membersList,"/projects/"+projectId+"/members/assign/paging",{pageTo: pageTo,project_id: projectId},checkedData);
                $(this).parent().parent().find('a').removeClass('pagingActive');
                $(this).addClass('pagingActive');
        });
    }
    //handler close button
    if($(btnClosePopup).length > 0){
        $(btnClosePopup).on('click',function(e){
            oldData.find('input[name="checkbox"]').not(':disabled').map(function(){
                $(this).find('span').removeClass('checked');
                $(this).prop('checked', false);
           });
            $('.roleList').find('input[type="radio"]').map(function(){
                $(this).find('span').removeClass('checked');
                $(this).prop('checked', false);
           });
            $('.pagination').find('a').removeClass('pagingActive');
            $(membersList).html('').append(oldData);
            $(searchMember).val('');
            $('.error-message').find('strong').html('')
        });
    }
    if($('.btn-cancel-popup').length > 0){
        $('.btn-cancel-popup').on('click',function(e){
            oldData.find('input[type="checkbox"]').not(':disabled').map(function(){
                $(this).find('span').removeClass('checked');
                $(this).prop('checked', false);
           });
            $('.roleList').find('input[type="radio"]').map(function(){
                $(this).find('span').removeClass('checked');
                $(this).prop('checked', false);
           });
            $('.pagination').find('a').removeClass('pagingActive');
            $(membersList).html('').append(oldData);
            $(searchMember).val('');
            $('.error-message').find('strong').html('')
        });
    }
    //handler search member
    if($(searchMember).length >0){
        var keyword = $(searchMember).val(),
            self = $(searchMember);
        $(searchMember).autocomplete({
            source: function( request, response ) {
                $.ajax( {
                  url: "../assign/search",
                  method: 'POST',
                  dataType: "json",
                  data: {
                    term: request.term,
                    _token: CSRF_TOKEN,
                    project_id: projectId
                  },
                  success: function( data ) {
                      if(data.wrong_key){
                          $('.pagination').find('a').removeClass('pagingActive');
                          $('.pagination').show();
                          oldData.find('input[type="checkbox"]').not(':disabled').map(function(){
                              $(this).find('span').removeClass('checked');
                              $(this).prop('checked', false);
                          });
                          $(membersList).html('').append(oldData);
                      }else{
                          var members = '';
                          data.map(function(item){
                              members += "<div class='col-lg-4 vert-offset-top-1'>\
                                  <label class='label-checkbox'>" +
                                  "<input type='checkbox'"+
                                  "name="+( item.existed ? "existed_members[] disabled=disabled checked=checked" : "members[]" )
                                  +" value="+item.id+">"+
                                  "<span class='custom-checkbox'></span>"+item.last_name+" "+item.first_name+" - "+item.member_code+"</label>\
                                  </div>";
                          });
                          $('#pagination_member').hide();
                          $(membersList).html('').append(members);
                      }
                    }
                });
              },
            minLength: 0,
            search: function(event, ui){},
            open: function(event, ui){
                $(".ui-autocomplete").hide();
            },
        });
    }
    
    //open popup edit member
    if($('.editMember').length > 0){
        $('.editMember').on('click',function(){
            var code = $(this).attr('code'),
                role_id = $(this).attr('role_id'),
                member_name = $(this).attr('member');
            $('#frmEditMember').find('input[type=radio]').each(function(i,e){
                $(e).find('span').removeClass('checked');
                $(e).prop('checked', false);
                if($(this).val() == role_id){
                    $(this).find('span').addClass('checked');
                    $(this).prop('checked', true);
                }
            });
            $('#editMemberPopup').find('.member_name').html(member_name+':');
            $('#frmEditMember').find('input[name=code]').val(code);
            $('#frmEditMember').find('input[name=ex_role]').val(role_id);
            $('#editMemberPopup').show();
        });
    }
    
    //open popup delete
    if($('.delete').length > 0 ){
        $('.delete').on('click',function() {
            var role_id = $(this).attr('role_id');
            $('#frmDeleteMember').find('input[name=ex_role]').val(role_id);
            $('#dataId').val($(this).attr('delete'));
            $('#deleteModal').modal({'show': true});
        });
    }
    
    if($('.remove').length > 0 ){
        $('.remove').on('click',function() {
            var role_id = $(this).attr('role_id');
            $('#frmRemoveMember').find('input[name=ex_role]').val(role_id);
            $('#dataIdRemove').val($(this).attr('remove'));
            $('#removeModal').modal({'show': true});
        });
    }
    
    //open popup restore
    if($('.restore').length > 0 ){
        $('.restore').on('click',function() {
            var role_id = $(this).attr('role_id');
            $('#frmRestoreMember').find('input[name=ex_role]').val(role_id);
            $('#dataIdRestore').val($(this).attr('restore'));
            $('#restoreModal').modal({'show': true});
        });
    }
});

function getMemberFromJson(method,storeEle,api,params = []){
    var data = '';
        $.ajax({
            method: method,
            url: api,
            data: { params: params,_token: CSRF_TOKEN},
            dataType: "json"
          }).done(function( respone ) {
              respone.map(function(item){
                  data += "<div class='col-lg-4 vert-offset-top-1'>\
                  <label class='label-checkbox'>" +
                  "<input type='checkbox'"+
                  "name="+( item.existed ? "existed_members[] disabled=disabled checked=checked" : "members[]" )
                  +" value="+item.id+">"+
                  "<span class='custom-checkbox'></span>"+item.last_name+" "+item.first_name+" - "+item.member_code+"</label>\
                  </div>";
              });
              if(!params.hasOwnProperty("key") ){
                  var wrapper = $(storeEle).html('').hide();
                      wrapper.fadeIn('300').append(data);
              }else{
                  var wrapper = $(storeEle).html('').append(data);
              }
          });
}