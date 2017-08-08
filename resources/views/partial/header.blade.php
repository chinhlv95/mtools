<div id="top-nav" class="skin-6 fixed">
    <div class="brand">
        <img src="{{ asset('/img/co-well-logo.png') }}" alt="Cowell Asia">
    </div><!-- /brand -->
    <button type="button" class="navbar-toggle pull-left" id="sidebarToggle">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <button type="button" class="navbar-toggle pull-left hide-menu" id="menuToggle">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    @if($user = Sentinel::check())
        <ul class="nav-notification clearfix">
            <li class="profile dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <strong>{{ $user->last_name }} {{ $user->first_name }}</strong>
                    <span><i class="fa fa-chevron-down"></i></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a class="clearfix" href="#">
                            @if($user->member_code > 0)
                                <img src="{{ asset('/img/avatar/'.$user->member_code.'.png') }}" alt="User Avatar">
                            @else
                                <img src="{{ asset('/img/default-avatar.png') }}" alt="User Avatar">
                            @endif
                        </a>
                    </li>
                    <li><a tabindex="-1" href="{{ URL::route('edit.profile') }}"><i class="fa fa-user fa-lg"></i> Profile</a></li>
                    <li><a tabindex="-1" href="{{ URL::route('change.password') }}"><i class="fa fa-user fa-lg"></i> Change Password</a></li>
                    <li><a tabindex="-1" href="{{ URL::to('logout') }}"><i class="fa fa-lock fa-lg"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    @else
        <ul class="nav-notification clearfix">
            <li><a tabindex="-1" href="#"><i class="fa fa-lock fa-lg"></i> Login</a></li>
        </ul>
    @endif
    <a href="javascript:void(0);" id="open_chm" root={{ asset('') }}>
        <img src="{{ asset('img/ico_help.png') }}">
    </a>
    <a href="javascript:void(0);" id="open_chm_jp" root={{ asset('') }}>
        <img src="{{ asset('img/icon_help_jp.png') }}">
    </a>
</div><!-- /top-nav-->