(function($){
    'use strict';
    
    //handler for check all
    var btnCheckAll = '.btn_delete_all',
        checkAll = '.check_box_all',
        checkDelete = '.check_box_delete',
        tblRoles = '.tblRoles',
        btnDelete = '.btnDelete',
        btnDeleteModal = '.btnDeleteModal',
        CSRF_TOKEN = $('input[name="_token"]').val(),
        mesageError = '.modal-body p';
    
    $(btnCheckAll).attr('disabled','disabled');
    //toogle disable button delete when change checkall checkbox
    $(tblRoles).on('change',checkAll,function(){
        $(tblRoles).find('input[type=checkbox]').prop('checked', $(this).prop("checked"));
        toogleDisabled(checkDelete,btnCheckAll);
    });
    
    if($('input[class="check_box_delete"]').length > 0){
       //check listener of checkbox to disable,enable button delete
        $(tblRoles).on('change',checkDelete,function(i){
               var checkedTotal = $(tblRoles).find('input[class="check_box_delete"]:checked').length,
                   unchkedTotal = $(tblRoles).find('input[class="check_box_delete"]:not(:checked)').length;
               
               if(checkedTotal == 0){
                   $(btnCheckAll).attr('disabled','disabled');
                   $(checkAll).removeAttr('checked');
               }else{
                   $(btnCheckAll).removeAttr('disabled');
               }
               $(checkAll).parent().find('span:before').css('display','none');
               if(unchkedTotal == 1){
                     $(checkAll).find('span').removeClass('checked');
                     $(checkAll).prop('checked', false);
               }else if(unchkedTotal == 0){
                   $(checkAll).find('span').addClass('checked');
                   $(checkAll).prop('checked', true);
               }
        });
    }else{
        $(checkAll).attr('disabled','disabled');
    }
    
    //delete one role
    $(tblRoles).on('click',btnDelete,function(e){
        e.preventDefault();
        var role_id = $(this).attr('value');
        $('#roleId').val(role_id);
        
        $.ajax({
            url: '/setting/roles/destroy',
            method: 'POST',
            data: { _check_id: role_id, _token: CSRF_TOKEN }
          }).done(function( respone ) {
              $(mesageError).html('');
              if(respone > 0){
                  $(btnDeleteModal).hide();
                  $(mesageError).append("This group is still "+ respone +" members, can't delete .");
              }else{
                  $(btnDeleteModal).show();
                  $(mesageError).append('Do you want delete ?');
              }
              $('#deleteModal').modal({'show': true});
            });
    });
    
    //delete all role
    $(btnCheckAll).on('click',function(e){
        e.preventDefault();
        var checkedValue = [];
        $(tblRoles).find('input[class="check_box_delete"]:checked').map(function(){
            checkedValue.push($(this).val());
        });
        
        $.ajax({
            url: '/setting/roles/destroy',
            method: 'POST',
            dataType: 'json',
            data: { _token: CSRF_TOKEN , _roles_id: checkedValue }
          }).done(function( respone ) {
              var str = '';
              $.map(respone,function(val,i){
                  str += "<strong>" + val.names +"</strong> group is still "+ val.users +" members, can't delete ."+"<br>";
              });
              $(mesageError).html('');
              //if stored members in roles in respone data > 0 => can't delete
              if(respone.length > 0){
                  $(btnDeleteModal).hide();
                  $(mesageError).append(str);
              }else{//if stored members in roles in respone data < 0 => delete all handler
                  $('#deleteAll').val(checkedValue);
                  $(btnDeleteModal).show();
                  $(mesageError).append('Do you want delete ?');
              }
              $('#deleteModal').modal({'show': true});
            });
    });
    
    
    $(document).find('input[type=checkbox]:checked').map(function(){
        $(this).removeAttr('disabled');
    });
    
    //handler checked for root 
    toogleChecked('view_list_project');   
    toogleChecked('view_project_info'); 
    toogleChecked('view_version');
    toogleChecked('view_kpt');
    toogleChecked('view_list_risk');
    toogleChecked('view_member');
    toogleChecked('view_project_cost');
    toogleChecked('view_defect');
    toogleChecked('view_roles');
  //enable parent node by root node checked onload
    enableCheckedOnload('view_list_project');
    enableCheckedOnload('view_project_info'); 
    enableCheckedOnload('view_version');
    enableCheckedOnload('view_kpt');
    enableCheckedOnload('view_list_risk');
    enableCheckedOnload('view_member');
    enableCheckedOnload('view_project_cost');
    enableCheckedOnload('view_defect');
    enableCheckedOnload('view_roles');
    
    //handler for check all link
    $('#checkPerAll').on('click',function(e){
        e.preventDefault();
        $('input[type="checkbox"]').removeAttr('disabled').prop('checked', !$(this).prop("checked"));
    });
    
    //define exception for uncheck root element
    var notUncheckElement = ['view_list_project',
                             'view_defect',
                             'admin_setting',
                             'view_roles',
                             'view_project_cost',
                             'view_personal_cost',
                             'view_list_project',
                             'view_quality_report_by_project',
                             'view_quality_report_by_member'];
    
  //handler for uncheck all element
    $('#UncheckPerAll').on('click',function(e){
        e.preventDefault();
        $('#frmAddRole').find('input[type="checkbox"]').map(function(i,e){
            if(notUncheckElement.indexOf($(this).attr('root')) == -1 ){
                $(this).removeAttr('checked').attr('disabled','disabled');
            }else{
                $(this).removeAttr('checked');
            }
        });
    });
    
    //handler for reset
    $('#roleReset').on('click',function(e){
        $('#roleName').val('');
        $('#slug').val('');
        $('#frmAddRole').find('input[type="checkbox"]').map(function(i,e){
            if(notUncheckElement.indexOf($(this).attr('root')) == -1 ){
                $(this).removeAttr('checked').attr('disabled','disabled');
            }else{
                $(this).removeAttr('checked');
            }
        });
    });
    
    //dynamically render slug
    renderSlug('#roleName','#slug');
    
    //auto focus on input role name
    $('#roleName').focus();      
    
    //handler update button for one click
    $('#btnUpdate').on('click',function(){
        $('#frmAddRole').submit();
        $(this).attr('disabled',true);
    });
    
    var checkedEle = [];
    $('input[type=checkbox]:checked').map(function(){
        checkedEle.push($(this).val());
    });
    //handler trigger root when click button reset
    $('#btnReset').on('click',function(){
        $('input[type=checkbox]').removeAttr('checked');
        $('input[type=checkbox]').map(function(){
            if($.inArray($(this).val(),checkedEle) > -1){
                $(this).removeAttr('disabled');
                $(this).prop('checked',true);
            }
        });
        $('input[root]').map(function(){
            if($(this).is(':checked')){
                $('input[parent='+$(this).val()+']').removeAttr('disabled');
            }else{
                $('input[parent='+$(this).val()+']').attr('disabled','disabled');
            }
        });
    });
})(jQuery);

function toogleChecked(root){
    var r = $('input[root ='+root+']'),
        pr = $('input[parent ='+root+']');
    
    r.on('change',function(){
        pr.prop('disabled', !$(this).prop('checked'));
        if(!$(this).prop('checked'))
            pr.removeAttr('checked');
    });
    if(root == 'view_list_project'){
        r.on('change',function(){
            var self = $(this);
            $('.structure_group').find('input[type="checkbox"]').not(self).map(function(){
                if(!$(self).is(':checked')){
                    $(this).removeAttr('checked').not(self).attr('disabled','disabled');
                }
            });
        });
    }
    if(root == 'view_project_info'){
        r.on('change',function(){
            var self = $(this);
            $('input[grandnode="view_project_info"]').not(self).map(function(){
                $(this).removeAttr('checked');
                $(this).attr('disabled','disabled');
            });
        });
    }
}

//enable checkbox of root when load update
function enableCheckedOnload(root){
    $('input[root='+root+']').map(function(){
        if($(this).is(':checked')){
            $('input[parent='+ root +']').removeAttr('disabled');
        }
    });
}

//handler render slug
function renderSlug(inputText,slugInput){
    var str = '',
        fillter = '',
        pattern = /[^a-zA-Z./-// /]/g;
    $(inputText).on('keyup',function(event){
        str = $(this).val().toLowerCase();
        fillter = str.replace(pattern,"_");
        $(slugInput).val(str);
    });
}

function toogleDisabled(ele,object){
    $(ele).each(function(i,e){
        if($(this).is(':checked')){
            $(object).removeAttr('disabled');
        }else{
            $(object).attr('disabled','disabled');
        }
    });
}
