 <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="/dashboard">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>

        @if(auth()->user()->isAdminOrAbove())

          <li class="nav-item">
            <a class="nav-link" href="/roles">
              <i class="mdi mdi-shield-account menu-icon"></i>
              <span class="menu-title">Roles</span>
            </a>
          </li>
            <li class="nav-item">
                <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}" href="/users">
                    <i class="mdi mdi-account menu-icon icon-head"></i>
                    <span class="menu-title">Users</span>
                </a>
            </li>

            <li class="nav-item">

                <a
                    class="nav-link {{ request()->routeIs('login-logs.*') ? 'active' : '' }}"
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

            <li class="nav-item {{ request()->routeIs('knowledge-base.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('knowledge-base.index') }}" >
                    <i class="mdi mdi-bookshelf menu-icon icon-head"></i>
                    <span class="menu-title">
                        Knowledge Base
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
