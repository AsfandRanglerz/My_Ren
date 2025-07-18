<div class="main-sidebar sidebar-style-2">

    <aside" id="sidebar-wrapper">

        <div class="mt-4 sidebar-brand">

            <a href="{{ url('/admin/dashboard') }}">

                <img alt="image" src="{{ asset('public/admin/assets/img/logo.png') }}" class="header-logo" />

                {{-- <span class="logo-name">Crop Secure</span> --}}

            </a>

        </div>

        <ul class="sidebar-menu">

            <li class="menu-header">Main</li>

            <li class="dropdown {{ request()->is('admin/dashboard') ? 'active' : '' }}">

                <a href="{{ url('/admin/dashboard') }}" class="nav-link"><i
                        data-feather="home"></i><span>Dashboard</span></a>

            </li>







            {{--  Roles --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Roles') && $sideMenuPermissions['Roles']->contains('view')))
                {{-- FAQS --}}

                <li class="dropdown {{ request()->is('admin/roles*') ? 'active' : '' }}">

                    <a href="{{ url('admin/roles') }}" class="nav-link"><i data-feather="lock"></i>

                        </i><span>Roles</span></a>

                </li>
            @endif







            {{--  SubAdmin --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Sub Admins') && $sideMenuPermissions['Sub Admins']->contains('view')))
                {{-- FAQS --}}

                <li class="dropdown {{ request()->is('admin/subadmin*') ? 'active' : '' }}">

                    <a href="{{ url('admin/subadmin') }}" class="nav-link"><i data-feather="user"></i><span>Sub

                            Admins</span></a>

                </li>
            @endif



            {{--  Users --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Users') && $sideMenuPermissions['Users']->contains('view')))
                <li class="dropdown {{ request()->is('admin/user*') ? 'active' : '' }}">

                    <a href="{{ url('admin/user') }}" class="nav-link">

                        <i data-feather="users"></i>

                        <span>Users</span>

                    </a>

                </li>
            @endif



















            {{--  Reward Settings --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Reward Settings') && $sideMenuPermissions['Reward Settings']->contains('view')))
                <li class="dropdown {{ request()->is('admin/login-reward-rules*') ? 'active' : '' }}">

                    <a href="{{ url('admin/login-reward-rules') }}" class="nav-link">

                        <i data-feather="gift"></i>

                        <span>Reward Settings</span>

                    </a>

                </li>
            @endif



            {{--  Products --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Devices/Products') && $sideMenuPermissions['Devices/Products']->contains('view')))
                <li class="dropdown {{ request()->is('admin/devices*') ? 'active' : '' }}">

                    <a href="{{ url('admin/devices') }}" class="nav-link">

                        <i data-feather="box"></i>

                        <span>Devices/Products</span>

                    </a>

                </li>
            @endif





            {{--  Points Conversition --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Voucher Settings') && $sideMenuPermissions['Voucher Settings']->contains('view')))
                <li class="dropdown {{ request()->is('admin/voucher*') ? 'active' : '' }}">

                    <a href="{{ url('admin/voucher-index') }}" class="nav-link">

                        <i data-feather="tag"></i>
                        <span>Voucher Settings</span>

                    </a>

                </li>
            @endif


            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Withdrawal Requests') &&
                        $sideMenuPermissions['Withdrawal Requests']->contains('view')))
                <li class="dropdown {{ request()->is('admin/withdrawrequest*') ? 'active' : '' }}">

                    <a href="{{ url('admin/withdrawrequest') }}" class="nav-link">

                        <i data-feather="arrow-down-left"></i>

                        <span>Withdrawal Requests</span>
                        <div id="withdrawalpendingCounter"
                            class="badge {{ request()->is('admin/withdrawrequest*') ? 'bg-white text-dark' : 'bg-purple text-white' }} rounded-circle"
                            style="display: inline-flex; justify-content: center; align-items: center; 
                            min-width: 22px; height: 22px; border-radius: 50%; 
                            text-align: center; font-size: 12px; margin-left: 5px; padding: 3px;">
                            0
                        </div>
                    </a>

                </li>
            @endif




            {{-- Ranking --}}
            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Users Rankings') && $sideMenuPermissions['Users Rankings']->contains('view')))
                <li class="dropdown {{ request()->is('admin/ranking*') ? 'active' : '' }}">
                    <a href="{{ url('admin/ranking') }}" class="nav-link">
                        <i data-feather="bar-chart-2"></i>
                        <span>Rankings</span>
                    </a>
                </li>
            @endif



            {{-- Notification --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Notifications') && $sideMenuPermissions['Notifications']->contains('view')))
                {{-- Notification --}}

                {{-- Notifications --}}

                {{-- <li class="dropdown {{ request()->is('admin/notification*') ? 'active' : '' }}">

                    <a href="

                {{ route('notification.index') }}

                " class="nav-link">

                        <i data-feather="bell"></i><span>Notifications</span>

                    </a>

                </li> --}}
            @endif





            {{--  About Us --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('About us') && $sideMenuPermissions['About us']->contains('view')))
                {{-- About Us --}}

                <li class="dropdown {{ request()->is('admin/about-us*') ? 'active' : '' }}">

                    <a href="{{ url('admin/about-us') }}" class="nav-link"><i
                            data-feather="help-circle"></i><span>About

                            Us</span></a>

                </li>
            @endif









            {{-- Contact Us  --}}





            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Contact us') && $sideMenuPermissions['Contact us']->contains('view')))
                {{-- Contact Us --}}

                <li class="dropdown {{ request()->is('admin/admin/contact-us*') ? 'active' : '' }}">

                    <a href="{{ url('admin/admin/contact-us') }}" class="nav-link"><i
                            data-feather="mail"></i><span>Contact

                            Us</span></a>

                </li>
            @endif





            {{--  FAQS --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Faqs') && $sideMenuPermissions['Faqs']->contains('view')))
                {{-- FAQS --}}

                {{-- <li class="dropdown {{ request()->is('admin/faq*') ? 'active' : '' }}">

                    <a href="{{ url('admin/faq-index') }}" class="nav-link"><i

                            data-feather="settings"></i><span>FAQ's</span></a>

                </li> --}}
            @endif



            {{--  Privacy Policy --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Privacy & Policy') && $sideMenuPermissions['Privacy & Policy']->contains('view')))
                {{--  Privacy Policy --}}

                <li class="dropdown {{ request()->is('admin/privacy-policy*') ? 'active' : '' }}">

                    <a href="{{ url('admin/privacy-policy') }}" class="nav-link"><i
                            data-feather="shield"></i><span>Privacy

                            & Policy</span></a>

                </li>
            @endif





            {{--  Terms & Conditions --}}



            @if (Auth::guard('admin')->check() ||
                    ($sideMenuPermissions->has('Terms & Conditions') &&
                        $sideMenuPermissions['Terms & Conditions']->contains('view')))
                <li class="dropdown {{ request()->is('admin/term-condition*') ? 'active' : '' }}">

                    <a href="{{ url('admin/term-condition') }}" class="nav-link"><i
                            data-feather="file-text"></i><span>Terms

                            & Conditions</span></a>

                </li>
            @endif







        </ul>

        </aside>

</div>
