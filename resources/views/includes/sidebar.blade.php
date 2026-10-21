 <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item {{ Request::is('dashboard') ? 'active' : '' }}">
            <a class="nav-link" href="/dashboard">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>

        @if(auth()->user()->isAdminOrAbove())

          <li class="nav-item {{ Request::is('roles*') ? 'active' : '' }}">
            <a class="nav-link" href="/roles">
              <i class="mdi mdi-shield-account menu-icon"></i>
              <span class="menu-title">Roles</span>
            </a>
          </li>
            <li class="nav-item {{ request()->routeIs('lead-stages.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('lead-stages.index') }}">
                    <i class="mdi mdi-format-list-checks menu-icon"></i>
                    <span class="menu-title">Stage Management</span>
                </a>
            </li>
            <li class="nav-item {{ Request::is('users*') ? 'active' : '' }}">
                <a class="nav-link" href="/users">
                    <i class="mdi mdi-account menu-icon icon-head"></i>
                    <span class="menu-title">Users</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('login-logs.*') ? 'active' : '' }}">

                <a
                    class="nav-link"
                    href="{{ route('login-logs.index') }}"
                >

                    <i class="mdi mdi-login-variant menu-icon"></i>

                    <span class="menu-title">
                        Login Logs
                    </span>

                </a>

            </li>
        @endif
       @php
            $isLeads = request()->routeIs('leads.*');
        @endphp

            <li class="nav-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">

                <a class="nav-link" href="{{ route('leads.index') }}" >

                    <i class="mdi mdi-chart-bar menu-icon icon-head"></i>

                    <span class="menu-title">
                        Leads
                    </span>

                </a>

            </li>

            {{-- Attendance section is Admin/Super Admin only. Everyone's
                 attendance is still recorded (login/logout + breaks from
                 the header widget). --}}
            @if(auth()->user()->isAdminOrAbove())
            <li class="nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('attendance.index') }}">
                    <i class="mdi mdi-calendar-clock menu-icon icon-head"></i>
                    <span class="menu-title">Attendance</span>
                </a>
            </li>
            @endif

            <li class="nav-item {{ request()->routeIs('knowledge-base.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('knowledge-base.index') }}" >
                    <i class="mdi mdi-bookshelf menu-icon icon-head"></i>
                    <span class="menu-title">
                        {{ auth()->user()->isAe() ? 'AE Knowledge Base' : 'Knowledge Base' }}
                    </span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link"
                href="{{ url('/chatify') }}"
                rel="noopener noreferrer">

                    <i class="ti-comments menu-icon"></i>

                    <span class="menu-title">Chat</span>

                    <span
                        id="chat-unread-count"
                        class="badge badge-danger ml-auto"
                        style="{{ ($unreadChatCount ?? 0) > 0 ? '' : 'display:none;' }}"
                    >
                        {{ $unreadChatCount ?? 0 }}
                    </span>
                </a>
            </li>
        </ul>
      </nav>
      <style>
        .nav-link.lead-active {
            background: #4b49ac !important;
            color: #fff !important;
        }
        .nav-link.lead-active:hover, .nav-link.lead-active:focus {
            text-decoration: none ;
            }
      </style>
      <!-- partial -->
