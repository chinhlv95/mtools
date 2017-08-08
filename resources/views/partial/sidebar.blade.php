<aside class="fixed skin-6">
    <div class="sidebar-inner scrollable-sidebars">
        <div class="size-toggle">
            <a class="btn btn-sm" id="sizeToggle">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
        </div><!-- /size-toggle -->
        <div class="user-block clearfix">
            @if($user->member_code > 0)
                <img src="{{ asset('/img/avatar/'.$user->member_code.'.png') }}" alt="User Avatar">
            @else
                <img src="{{ asset('/img/default-avatar.png') }}" alt="User Avatar">
            @endif
            <div class="detail">
                 <?php $explodeEmail = explode('@',$user->email); ?>
                <strong>{{ $explodeEmail[0] }}</strong> <span class="badge badge-danger bounceIn animation-delay4 m-left-xs">0</span>
            </div>
        </div><!-- /user-block -->
        <div class="main-menu">
            <ul>
                <li class="openable open {{ Route::currentRouteName() == 'rank.index' ? 'active' : null }}">
                    <a href="#">
                        <span class="menu-icon">
                            <i class="fa fa-list fa-lg"></i>
                        </span>
                        <span class="text">
                            Ranking
                        </span>
                        <span class="menu-hover"></span>
                    </a>
                    <ul class="submenu">
                        <li class="{{ Route::currentRouteName() == 'rank.index' ? 'active' : null }}">
                            <a href="{{URL::route('rank.index')}}" slug="defect-report"><span class="submenu-label">Ranking</span></a>
                        </li>
                    </ul>
                </li>
                <li class="openable open {{ (Route::currentRouteName() == 'setting.roles')
                                         || (Route::currentRouteName() == 'user-management.index')
                                         || (Route::currentRouteName() == 'content-management.index')
                                         || (Route::currentRouteName() == 'department.index')
                                         || (Route::currentRouteName() == 'user-mapping.index')
                                         || (Route::currentRouteName() == 'content-management.show')
                                         || (Route::currentRouteName() == 'content-management.create')
                                         || (Route::currentRouteName() == 'content-management.edit')
                                         || (Route::currentRouteName() == 'user-management.editUser')
                                         ? 'active' : null }}">
                  <?php $role = Sentinel::findRoleById(1); ?>
                  @if ($user->inRole($role->slug))
                    <a href="#">
                        <span class="menu-icon">
                            <i class="fa fa-cogs fa-lg"></i>
                        </span>
                        <span class="text">
                            Administration
                        </span>
                        <span class="menu-hover"></span>
                    </a>
                    <ul class="submenu">
                        <li class="{{ Route::currentRouteName() == 'setting.roles' ? 'active' : null }}">
                            <a href="{{ URL::route('setting.roles') }}" slug="roles-management">
                                <span class="submenu-label">Roles Management</span>
                            </a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'user-management.index'
                                     || Route::currentRouteName() == 'user-management.editUser'
                                     ? 'active' : null }}">
                            <a href="{{ URL::route('user-management.index') }}" slug="user-management">
                                <span class="submenu-label">User Management</span>
                            </a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'content-management.index'
                                    || Route::currentRouteName() == 'content-management.show'
                                    || Route::currentRouteName() == 'content-management.create'
                                    || Route::currentRouteName() == 'content-management.edit'
                                     ? 'active' : null }}">
                            <a href="{{ URL::route('content-management.index') }}" slug="content-mapping">
                                <span class="submenu-label">Content Mapping</span>
                            </a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'department.index' ? 'active' : null }}">
                            <a href="{{ URL::route('department.index') }}" slug="organization">
                                <span class="submenu-label">Organization</span>
                            </a>
                        </li>
                    </ul>
                  @endif
                </li>
                <li class="openable open {{   (Route::currentRouteName() == 'projects.index')
                                           || (Route::currentRouteName() == 'project.edit')
                                           || (Route::currentRouteName() == 'project.show')
                                           || (Route::currentRouteName() == 'projects.members.assign.index')
                                           || (Route::currentRouteName() == 'projects.risk.index')
                                           || (Route::currentRouteName() == 'version.index')
                                           || (Route::currentRouteName() == 'projects.kpt.list')
                                           || (Route::currentRouteName() == 'projects.ticket.list')
                                           || (Route::currentRouteName() == 'kpi.index')
                                           || (Route::currentRouteName() == 'risk.getCreate')
                                           || (Route::currentRouteName() == 'risk.getEdit')
                                           || (Route::currentRouteName() == 'kpt.get.new')
                                           || (Route::currentRouteName() == 'kpt.get.edit')
                                            ? 'active' : null }}">
                    <a href="#">
                        <span class="menu-icon">
                            <i class="fa fa-book fa-lg"></i>
                        </span>
                        <span class="text">
                            Project management
                        </span>
                        <span class="menu-hover"></span>
                    </a>
                    <ul class="submenu">
                        <li class="{{ Route::currentRouteName() == 'projects.index' ? 'active' : null }}">
                            <a href="{{ URL::route('projects.index') }}" slug="project">
                                <span class="submenu-label">Project list</span>
                            </a>
                        </li>
                        <?php
                            $adminId      = Helpers::getAdminOrDirectorId();
                            $userId       = Helpers::getIdOfUserLogin();
                        ?>
                        @if(isset($project_id))
                            @if ($user->hasAccess('user.update_project_info') || in_array($userId, $adminId))
                                <li class="{{ Route::currentRouteName() == 'project.edit' ? 'active' : null }}">
                                    <a href="{{ URL::route('project.edit' , $project_id )}}" slug="update-project-info"><span class="submenu-label">Update project info</span></a>
                                </li>
                            @endif
                            @if ($user->hasAccess('user.view_project_info') || in_array($userId, $adminId))
                                <li class="{{ Route::currentRouteName() == 'project.show' ? 'active' : null }}">
                                    <a href="{{ URL::route('project.show' , $project_id )}}" slug="view-project-info"><span class="submenu-label">View project info</span></a>
                                </li>
                            @endif
                            @if ($user->hasAccess('user.view_member') || in_array($userId, $adminId))
                                <li class="{{ Route::currentRouteName() == 'projects.members.assign.index' ? 'active' : null }}">
                                    <a href="{{ URL::route('projects.members.assign.index' , $project_id )}}" slug="member-management"><span class="submenu-label">Member management</span></a>
                                </li>
                            @endif
                            @if ($user->hasAccess('user.view_list_risk') || in_array($userId, $adminId))
                                <li class="{{ Route::currentRouteName() == 'projects.risk.index' || Route::currentRouteName() == 'risk.getCreate' || Route::currentRouteName() == 'risk.getEdit' ? 'active' : null }}">
                                    <a href="{{ URL::route('projects.risk.index' , $project_id )}}" slug="risk-management"><span class="submenu-label">Risk management</span></a>
                                </li>
                            @endif
                            @if ($user->hasAccess('user.view_version') || in_array($userId, $adminId))
                                <li class="{{ Route::currentRouteName() == 'version.index' ? 'active' : null }}">
                                    <a href="{{ URL::route('version.index' , $project_id )}}" slug="version-management"><span class="submenu-label">Version management</span></a>
                                </li>
                            @endif
                            @if ($user->hasAccess('user.view_kpt') || in_array($userId, $adminId))
                                <li class="{{ Route::currentRouteName() == 'projects.kpt.list' || Route::currentRouteName() == 'kpt.get.new' || Route::currentRouteName() == 'kpt.get.edit' ? 'active' : null }}">
                                    <a href="{{ URL::route('projects.kpt.list' , $project_id )}}" slug="kpt-management"><span class="submenu-label">KPT management</span></a>
                                </li>
                            @endif
                            @if ($user->hasAccess('user.view_project_info') || in_array($userId, $adminId))
                            <li class="{{ Route::currentRouteName() == 'kpi.index' ? 'active' : null }}">
                                <a href="{{ URL::route('kpi.index' , $project_id )}}" slug="kpi-management"><span class="submenu-label">KPI management</span></a>
                            </li>
                            @endif
                        @endif
                    </ul>
                </li>
                @if ($user->hasAccess('user.view_file_management') || in_array($userId, $adminId))
                    <li class="openable open {{ Route::currentRouteName() == 'file-management.index' ? 'active' : null }}">
                        <a href="#">
                            <span class="menu-icon">
                                <i class="fa fa-file-excel-o fa-lg"></i>
                            </span>
                            <span class="text">
                                File Management
                            </span>
                            <span class="menu-hover"></span>
                        </a>
                        <ul class="submenu">
                            <li class="{{ Route::currentRouteName() == 'file-management.index' ? 'active' : null }}">
                                <a href="{{URL::route('file-management.index')}}" slug="file-management"><span class="submenu-label">File Management</span></a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="openable open {{ Route::currentRouteName() == 'project.cost.index' ? 'active' : null }}">
                    <a href="#">
                        <span class="menu-icon">
                            <i class="fa fa-calendar fa-lg"></i>
                        </span>
                        <span class="text">
                            Cost report
                        </span>
                        <span class="menu-hover"></span>
                    </a>
                    <ul class="submenu">
                        <li class="{{ Route::currentRouteName() == 'project.cost.index' ? 'active' : null }}">
                            <a href="{{ URL::route('project.cost.index') }}" slug="project-cost"><span class="submenu-label">Project cost</span></a>
                        </li>
                    </ul>
                </li>
                <li class="openable open {{ Route::currentRouteName() == 'defect.report.list' ? 'active' : null }}">
                    <a href="#">
                        <span class="menu-icon">
                            <i class="fa fa-bar-chart fa-lg"></i>
                        </span>
                        <span class="text">
                            Defect report
                        </span>
                        <span class="menu-hover"></span>
                    </a>
                    <ul class="submenu">
                        <li class="{{ Route::currentRouteName() == 'defect.report.list' ? 'active' : null }}">
                            <a href="{{URL::route('defect.report.list')}}" slug="defect-report"><span class="submenu-label">Defect report</span></a>
                        </li>
                    </ul>
                </li>
                <li class="openable open {{ (Route::currentRouteName() == 'quality-report.project.index')
                                         || (Route::currentRouteName() == 'quality-report.member.index')
                                         || (Route::currentRouteName() == 'quality-report.project.member')
                                          ? 'active' : null }}">
                    <a href="#">
                        <span class="menu-icon">
                            <i class="fa fa-line-chart fa-lg"></i>
                        </span>
                        <span class="text">
                            Summary report
                        </span>
                        <span class="menu-hover"></span>
                    </a>
                    <ul class="submenu">
                        <li class="{{ Route::currentRouteName() == 'quality-report.project.index' ? 'active' : null }}">
                            <a href="{{ URL::route('quality-report.project.index') }}" slug="summary-report-project"><span class="submenu-label">By Project</span></a>
                        </li>
                        <li class="{{ Route::currentRouteName() == 'quality-report.member.index' ? 'active' : null }}">
                            <a href="{{ URL::route('quality-report.member.index') }}" slug="summary-report-member"><span class="submenu-label">By Member</span></a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div><!-- /main-menu -->
    </div><!-- /sidebar-inner -->
</aside>

