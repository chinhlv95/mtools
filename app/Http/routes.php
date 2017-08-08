<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/',['middleware' => 'sentinel.login', function() {
    return Redirect::to('projects');
}]);

// Login
Route::get('login', [
    'as' => 'login',
    'uses' => 'AuthController@login'
]);

Route::post('login', [
    'as' => 'login',
    'uses' => 'AuthController@processLogin'
]);

Route::group(['middleware' => 'sentinel.login', 'domain' => env('DOMAIN')], function () {
    Route::get('/fillcombobox', [
                    'as' => 'department.fillcombobox',
                    'uses' => 'DepartmentController@fillcombobox'
    ]);
    Route::get('logout', function() {
        Sentinel::logout();
        return Redirect::to('/login');
    });

    Route::get('/list/', [
        'as' => 'projects.members.list',
        'uses' =>  'MemberController@getListMembers'
    ]);

    Route::post('/store-member/',[
        'as' => 'projects.members.store',
        'uses' => 'MemberController@store'
    ]);

    Route::post('/assign/edit',[
        'as' => 'projects.members.edit',
        'uses' => 'MemberController@eidt'
    ]);


    Route::get('profiles/index','ProfilesController@index');

    Route::group(['prefix' => '-maa'], function () {
        Route::group(['middleware' =>['hasAccess'],
                'hasAccess' => ['user.view_list_project']], function () {
                Route::get('/',[
                        'as' => 'projects.index',
                        'uses' => 'ProjectController@index'
                ]);
            });
        Route::get('create',[
                'as' => 'projects.create',
                'uses' => 'ProjectController@create'
        ]);
        Route::post('create',[
                'as' => 'projects.store',
                'uses' => 'ProjectController@store'
        ]);
        Route::get('/edit/{id}', [
                'as' => 'project.edit',
                'uses' =>  'ProjectController@edit'
        ]);


        Route::post('/edit/upload/{id}',[
                'as' => 'projects.upload',
                'uses' => 'ProjectController@uploadFiles'
        ]);

        Route::post('/edit/uploads/delete/{project_id}',[
                'as' => 'projects.uploads.delete',
                'uses' => 'ProjectController@delete'
        ]);

        Route::get('/download/{file_name}',[
                'as' => 'projects.download',
                'uses' => 'ProjectController@download'
        ]);

            //search member ajax
        Route::post('/assign/search',[
            'as' => 'projects.members.assign.search',
            'uses' => 'MemberController@searchByAjax'
        ]);

        Route::post('/update/{id}', [
                'as' => 'project.update',
                'uses' =>  'ProjectController@update'
        ]);
        Route::get('/crawler/{id}', [
                'as' => 'project.crawleredit',
                'uses' => 'ProjectController@crawleredit'
        ]);
        Route::post('/crawler/{id}', [
                'as' => 'project.crawlerupdate',
                'uses' => 'ProjectController@crawlerupdate'
        ]);
        Route::get('{id}/editproject',[
                'as' => 'project.editproject',
                'uses' => 'ProjectController@editProject'
        ]);

        Route::Post('{id}/editproject', [
                'as' => 'project.updateproject',
                'uses' => 'ProjectController@updateProject'
        ]);

        Route::post('/fillter/{id}', [
                'as' => 'project.fillter',
                'uses' =>  'ProjectController@fillterDataByGroupIdWithAjax'
        ]);

        Route::post('/Inprogress/{id}', [
                'as' => 'project.Inprogress',
                'uses' => 'ProjectController@Inprogress'
        ]);
        Route::post('/active/{id}', [
                'as' => 'project.active',
                'uses' => 'ProjectController@active'
        ]);

        Route::post('sync', [
                'as' => 'project.sync',
                'uses' => 'ProjectController@sync'
        ]);

        Route::get('{id}/show',[
                'as' => 'project.show',
                'uses' => 'ProjectController@show'
        ]);

        Route::group(['prefix' => '{project_id}'], function () {
            //projects/{project_id}/manager
            Route::get('/report', [
                'as'   => 'projects.report',
                'uses' => 'ProjectController@ProjectReport'
            ]);
            //projects/{project_id}/manager/members
            Route::group(['prefix' => 'members'], function () {

                Route::get('/assign/index',[
                    'as' => 'projects.members.assign.index',
                    'uses' => 'MemberController@index'
                ]);

                Route::get('/list/', [
                    'as' => 'projects.members.list',
                    'uses' =>  'MemberController@getListMembers'
                ]);

                Route::post('/store-member/',[
                    'as' => 'projects.members.store',
                    'uses' => 'MemberController@store'
                ]);

                Route::post('/assign/edit',[
                    'as' => 'projects.members.edit',
                    'uses' => 'MemberController@eidt'
                ]);

                Route::post('/assign/update',[
                    'as' => 'projects.members.update',
                    'uses' => 'MemberController@update'
                ]);

                Route::post('/assign/delete',[
                    'as' => 'projects.members.delete',
                    'uses' => 'MemberController@delete'
                ]);

                Route::get('/assign/histories',[
                    'as' => 'projects.members.histories',
                    'uses' => 'MemberController@searchMember'
                ]);

                // get emails to search auto suggest
                Route::get('/emails',[
                    'as' => 'emails',
                    'uses' => 'MemberController@emailAutocomplete'
                ]);

                //pagination for ajax
                Route::get('/assign/paging',[
                    'as' => 'projects.members.assign.paging',
                    'uses' => 'MemberController@paging'
                ]);

                //search member ajax
                Route::post('/assign/search',[
                    'as' => 'projects.members.assign.search',
                    'uses' => 'MemberController@searchByAjax'
                ]);
            });

            Route::group(['prefix' => 'risk'], function () {
                Route::get('/list', [
                    'as' => 'projects.risk.index',
                    'uses' =>'RiskController@index'
                ])->where('id','[0-9]+');
                Route::get('/create', [
                    'as' => 'risk.getCreate',
                    'uses' =>  'RiskController@getCreate'
                ])->where('id','[0-9]+');
                Route::post('/postCreate', [
                    'as' => 'risk.postCreate',
                    'uses' =>  'RiskController@postCreate'
                ]);
                Route::get('/edit/{riskId}', [
                    'as' => 'risk.getEdit',
                    'uses' =>  'RiskController@getEdit'
                ]);
                Route::post('/postEdit/{riskId}', [
                    'as' => 'risk.postEdit',
                    'uses' =>  'RiskController@postEdit'
                ]);
                Route::post('/postDelete', [
                    'as' => 'risk.postDelete',
                    'uses' =>  'RiskController@postDelete'
                ]);
            });

            Route::group(['prefix' => 'kpt'], function (){
                Route::get('/list/',[
                    'as' => 'projects.kpt.list',
                    'uses' => 'KptController@index'
                ])->where('id','[0-9]+');

                Route::get('/new_kpt/',[
                    'as' => 'kpt.get.new',
                    'uses' => 'KptController@create'
                ]);

                Route::post('/new_kpt/',[
                    'as' => 'kpt.post.new',
                    'uses' => 'KptController@store'
                ]);

                Route::get('/edit/{kptId}', [
                    'as' => 'kpt.get.edit',
                    'uses' => 'KptController@edit'
                ]);

                Route::post('/edit/{kptId}', [
                    'as' => 'kpt.post.edit',
                    'uses' => 'KptController@update'
                ]);

                Route::post('/delete/', [
                    'as' => 'kpt.post.delete',
                    'uses' => 'KptController@destroy'
                ]);
            });

            Route::group(['prefix' => 'version'], function (){

                Route::get('/', [
                    'as' => 'version.index',
                    'uses' => 'VersionController@index'
                ]);

                Route::get('create',[
                    'as' => 'version.create',
                    'uses' => 'VersionController@create'
                ]);

                Route::post('create',[
                    'as' => 'version.store',
                    'uses' => 'VersionController@store'
                ]);

                Route::get('edit/{id}', [
                    'as' => 'version.edit',
                    'uses' => 'VersionController@edit'
                ]);

                Route::post('update/{id}', [
                    'as' => 'version.update',
                    'uses' => 'VersionController@update'
                ]);

                Route::post('delete/', [
                    'as' => 'version.delete',
                    'uses' => 'VersionController@destroy'
                ]);
            });
            Route::group(['prefix' => 'kpi'], function (){

                Route::get('/', [
                        'as' => 'kpi.index',
                        'uses' => 'ProjectKpiController@index'
                ]);

                Route::get('create',[
                        'as' => 'kpi.create',
                        'uses' => 'ProjectKpiController@create'
                ]);

                Route::post('create',[
                        'as' => 'kpi.store',
                        'uses' => 'ProjectKpiController@store'
                ]);

                Route::post('sync_old_data',[
                        'as' => 'kpi.sync',
                        'uses' => 'ProjectKpiController@sync'
                ]);

                Route::get('edit/{id}',[
                        'as' => 'kpi.edit',
                        'uses' => 'ProjectKpiController@edit'
                ]);

                Route::post('edit/{id}',[
                        'as' => 'kpi.update',
                        'uses' => 'ProjectKpiController@update'
                ]);

                Route::post('delete/', [
                        'as' => 'kpi.delete',
                        'uses' => 'ProjectKpiController@destroy'
                ]);
                Route::post('end_datepicker', [
                        'as' => 'kpi.datepicker',
                        'uses' => 'ProjectKpiController@selectDate'
                ]);
            });

            Route::group(['prefix' => 'ticket'], function (){
                Route::get('/list/',[
                                'as' => 'projects.ticket.list',
                                'uses' => 'TicketController@index'
                ])->where('id','[0-9]+');

                Route::post('/import_bug', [
                                'as' => 'project.bug.import',
                                'uses' =>'ImportController@importBug']);
            });
        });
    });

    Route::group(['prefix' => 'cost'], function () {
        Route::group(['prefix' => 'project_cost'], function () {
            Route::get('/list', [
                            'as' => 'project.cost.index',
                            'uses' => 'ProjectCostController@index'
            ]);

            Route::post('/check_file_name',[
                            'as' => 'project.cost.checkFileName',
                            'uses' => 'ImportController@checkFileUploadedOrNot'
            ]);

            Route::post('/import', [
                            'as' => 'project.cost.import',
                            'uses' => 'ImportController@import'
            ]);

            Route::post('/import_after_confirm', [
                            'as' => 'project.cost.import.after',
                            'uses' => 'ImportController@importAfterConfirm'
            ]);

            Route::post('/export_file_cost_after_import',[
                            'as' => 'project.cost.export.after.import',
                            'uses' => 'ImportController@fillTicketIdToExport'
            ]);

            Route::post('/export/export_month', [
                            'as' => 'project.cost.export.month',
                            'uses' => 'ExportController@export'
            ]);

            Route::post('/export/export_total_cost', [
                            'as' => 'project.cost.export.total',
                            'uses' => 'ExportController@exportTotal'
            ]);

            Route::post('/export/export_cost', [
                            'as' => 'project.cost.export.cost',
                            'uses' => 'ExportController@exportCost'
            ]);
        });

        Route::group(['prefix' => 'personal_cost'], function () {
            Route::get('/list', [
                            'as' => 'personal.cost.index',
                            'uses' => 'PersonalCostController@index'
            ]);
        });
    });

    Route::group(['prefix' => 'defect-report'], function () {
        Route::get('/',[
                        'as' => 'defect.report.list',
                        'uses' => 'DefectReportController@index'
        ]);

        Route::post('/export',[
                        'as' => 'defect.report.export',
                        'uses' => 'DefectReportController@export'
        ]);
        Route::post('/import_bug', [
                        'as' => 'project.bug.import',
                        'uses' => 'ImportController@import'
        ]);

        Route::post('/import_bug_after_confirm', [
                        'as' => 'project.bug.import.after',
                        'uses' => 'ImportController@importAfterConfirm'
        ]);

        Route::post('/check_file_name',[
                        'as' => 'project.bug.checkFileName',
                        'uses' => 'ImportController@checkFileUploadedOrNot'
        ]);

        Route::post('/export_file_after_import',[
                        'as' => 'project.bug.export.after.import',
                        'uses' => 'ImportController@fillTicketIdToExport'
        ]);
    });

    Route::group(['prefix' => 'file-management'], function () {
        Route::get('/', [
            'as' => 'file-management.index',
            'uses' => 'FileManagementController@index'
        ]);
    });

    Route::group(['prefix' => 'ticket-type'], function (){

        Route::get('/', [
                'as' => 'ticket_type.index',
                'uses' => 'TicketTypeController@index'
        ]);

        Route::get('create',[
                'as' => 'ticket_type.create',
                'uses' => 'TicketTypeController@create'
        ]);

        Route::post('create',[
                'as' => 'ticket_type.store',
                'uses' => 'TicketTypeController@store'
        ]);

        Route::get('edit/{id}', [
                'as' => 'ticket_type.edit',
                'uses' => 'TicketTypeController@edit'
        ]);

        Route::post('update/{id}', [
                'as' => 'ticket_type.update',
                'uses' => 'TicketTypeController@update'
        ]);

        Route::post('delete/', [
                'as' => 'ticket_type.delete',
                'uses' => 'TicketTypeController@destroy'
        ]);
    });

    Route::group(['prefix' => 'quality-report'], function (){
        Route::get('/project',[
                'as' => 'quality-report.project.index',
                'uses' => 'QualityReportByProjectController@index'
        ]);
        Route::get('/project/show',[
                        'as' => 'quality-report.project.show',
                        'uses' => 'QualityReportByProjectController@getReportByProject'
        ]);
        Route::get('/member',[
                        'as' => 'quality-report.member.index',
                        'uses' => 'QualityReportByMemberController@index'
        ]);
        Route::get('/member/show',[
                        'as' => 'quality-report.member.show',
                        'uses' => 'QualityReportByMemberController@getReportByMember'
        ]);
        Route::get('/member-in-projects',[
                        'as' => 'quality-report.project.member',
                        'uses' => 'QualityReportByPmController@getReportByMemberInProjects'
        ]);
    });

    //route for group setting admin
    Route::group(['middleware' => 'sentinel.admin','prefix' => 'setting'], function (){
        Route::get('/roles',[
                'as' => 'setting.roles',
                'uses' => 'RolesController@index'
        ]);
        Route::get('/roles/create',[
                'as' => 'setting.roles.create',
                'uses' => 'RolesController@create'
        ]);
        Route::post('/roles/store',[
                        'as' => 'setting.roles.store',
                        'uses' => 'RolesController@store'
        ]);
        Route::get('/roles/edit/{id}',[
                'as' => 'setting.roles.edit',
                'uses' => 'RolesController@edit'
        ]);
        Route::post('/roles/update/',[
                'as' => 'setting.roles.update',
                'uses' => 'RolesController@update'
        ]);
        Route::post('/roles/destroy/',[
                'as' => 'setting.roles.destroy',
                'uses' => 'RolesController@destroy'
        ]);
        Route::group(['prefix' => 'user-management'], function () {
            Route::get('/', [
                            'as' => 'user-management.index',
                            'uses' => 'UserManagementController@index'
            ]);
            Route::get('/edit/{id}', [
                            'as' => 'user-management.edit',
                            'uses' => 'UserManagementController@edit'
            ]);
            Route::get('/lock/{id}', [
                            'as' => 'user-management.lockUser',
                            'uses' => 'UserManagementController@lockUser'
            ]);
            Route::get('/edit-user/{id}', [
                'as' => 'user-management.editUser',
                'uses' => 'UserManagementController@editUser'
            ]);
            Route::post('/edit-user/{id}', [
                'as' => 'user-management.editUser',
                'uses' => 'UserManagementController@processEditUser'
            ]);

        });
        Route::group(['prefix' => 'content-management'], function () {
            Route::get('/', [
                            'as' => 'content-management.index',
                            'uses' => 'ManagementController@index'
            ]);
            Route::get('/search', [
                            'as' => 'content-management.show',//search resource
                            'uses' => 'ManagementController@show'
            ]);
            Route::get('/create', [
                            'as' => 'content-management.create',
                            'uses' => 'ManagementController@create'
            ]);
            Route::get('/edit/{id}/{source_id}/{type_id}', [
                            'as' => 'content-management.edit',
                            'uses' => 'ManagementController@edit'
            ]);
            Route::post('/store', [
                            'as' => 'content-management.store',
                            'uses' => 'ManagementController@store'
            ]);
            Route::post('/update/', [
                            'as' => 'content-management.update',
                            'uses' => 'ManagementController@update'
            ]);
        });
        Route::group(['prefix' => 'user-mapping'], function () {
            Route::get('/', [
                            'as' => 'user-mapping.index',
                            'uses' => 'UserMappingController@index'
            ]);
            Route::get('/search', [
                            'as' => 'user-mapping.show',
                            'uses' => 'UserMappingController@show'
            ]);
            Route::post('/update', [
                            'as' => 'user-mapping.update',
                            'uses' => 'UserMappingController@update'
            ]);
       });
    });

    Route::group(['middleware' =>['sentinel.admin'], 'prefix' => 'structure'], function () {
        Route::get('/', [
                'as' => 'department.index',
                'uses' => 'DepartmentController@index'
        ]);
        Route::get('create', [
                'as' => 'department.create',
                'uses' => 'DepartmentController@create'
        ]);
        Route::post('create', [
                'as' => 'department.store',
                'uses' => 'DepartmentController@store'
        ]);
        Route::get('{id}/edit', [
                'as' => 'department.edit',
                'uses' => 'DepartmentController@edit'
        ]);
        Route::post('{id}/update', [
                'as' => 'department.update',
                'uses' => 'DepartmentController@update'
        ]);
        Route::post('/delete', [
                'as' => 'department.delete',
                'uses' => 'DepartmentController@destroy'
        ]);
        Route::post('/getprojects', [
                'as' => 'department.getprojects',
                'uses' => 'DepartmentController@getProject'
        ]);
    });


    Route::group(['prefix' => 'bug-type'], function () {
        Route::get('/', [
                'as' => 'bug-type.index',
                'uses' => 'BugTypeController@index'
        ]);
        Route::get('create', [
                'as' => 'bug-type.create',
                'uses' => 'BugTypeController@create'
        ]);
        Route::post('create', [
                'as' => 'bug-type.store',
                'uses' => 'BugTypeController@store'
        ]);
        Route::get('{id}/edit', [
                'as' => 'bug-type.edit',
                'uses' => 'BugTypeController@edit'
        ]);
        Route::post('{id}/update', [
                'as' => 'bug-type.update',
                'uses' => 'BugTypeController@update'
        ]);
        Route::post('/delete', [
                'as' => 'bug-type.delete',
                'uses' => 'BugTypeController@destroy'
        ]);
    });

    Route::get('edit-profile', [
        'as' => 'edit.profile',
        'uses' => 'AuthController@editProfile'
    ]);
    Route::post('edit-profile', [
        'as' => 'edit.profile',
        'uses' => 'AuthController@processEditProfile'
    ]);

    Route::get('change-password', [
        'as' => 'change.password',
        'uses' => 'AuthController@changePassword'
    ]);
    Route::post('change-password', [
        'as' => 'change.password',
        'uses' => 'AuthController@processChangePassword'
    ]);

    Route::group(['prefix' => 'rank'], function () {
        Route::get('/', [
                        'as' => 'rank.index',
                        'uses' => 'RankController@index'
        ]);
        Route::post('/info_project', [
                        'as' => 'rank.infoProject',
                        'uses' => 'RankController@infoProject'
        ]);
        Route::post('/info_dev', [
                        'as' => 'rank.infoDev',
                        'uses' => 'RankController@infoDev'
        ]);
        Route::post('/info_qa', [
                        'as' => 'rank.infoQa',
                        'uses' => 'RankController@infoQa'
        ]);
    });
});

// Route for api
Route::group(['prefix' => 'api/v2'], function (){
    Route::get('/projects',[
                    'as' => 'api.project',
                    'uses' => 'QualityReportByProjectController@getProjectReportApi'
    ]);
    Route::get('/members',[
                    'as' => 'api.member',
                    'uses' => 'QualityReportByMemberController@getMemberReportApi'
    ]);
    Route::get('/project-member',[
                    'as' => 'api.project.member',
                    'uses' => 'QualityReportByProjectController@getProjectMemberApi'
    ]);
    Route::get('/member-on-project',[
                    'as' => 'api.member.on.project',
                    'uses' => 'QualityReportByPmController@getMemberReportWithProjectApi'
    ]);
});

Route::get('get_department', function () {
    Artisan::call('portal_departments:get');
    return redirect(Route('department.index'))
        ->withSuccess(Lang::get('message.get_department_seccess'));
});