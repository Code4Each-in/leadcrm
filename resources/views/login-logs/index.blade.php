@extends('layout')
@section('title', ' Users')
@section('subtitle', 'Users')
@section('content')

<div class="content-wrapper">

    <div class="row">

        <div class="col-12">

            <div class="card">

                <div class="card-body">

                    {{-- Page Header --}}
                    <div class="mb-4">

                        <h4 class="card-title mb-1">
                            Login Logs
                        </h4>

                        <p class="text-muted mb-0">
                            Track user login and logout activity.
                        </p>

                    </div>


                    {{-- Filters --}}
                    <form
                        method="GET"
                        action="{{ route('login-logs.index') }}"
                         id="loginLogFilters"
                        class="mb-4"
                    >

                    <div class="row align-items-end">

                        {{-- User --}}
                        <div class="col-md-3">

                            <div class="form-group mb-0">

                                <label class="font-weight-bold">
                                    User
                                </label>

                                <select
                                    id="filterUser"
                                    name="user_id"
                                    class="form-control"
                                >
                                    <option value="">
                                        All Users
                                    </option>

                                    @foreach($users as $user)

                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        {{-- Role --}}
                        <div class="col-md-3">

                            <div class="form-group mb-0">

                                <label class="font-weight-bold">
                                    Role
                                </label>

                                <select
                                    id="filterRole"
                                    name="role_id"
                                    class="form-control"
                                >
                                    <option value="">
                                        All Roles
                                    </option>

                                    @foreach($roles as $role)

                                        <option value="{{ $role->id }}">
                                            {{ $role->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        {{-- Login --}}
                        <div class="col-md-2">

                            <div class="form-group mb-0">

                                <label class="font-weight-bold">
                                    Login
                                </label>

                                <input
                                    type="date"
                                    id="filterLogin"
                                    name="login"
                                    class="form-control"
                                    max="{{ date('Y-m-d') }}"
                                >

                            </div>

                        </div>


                        {{-- Logout --}}
                        <div class="col-md-2">

                            <div class="form-group mb-0">

                                <label class="font-weight-bold">
                                    Logout
                                </label>

                                <input
                                    type="date"
                                    id="filterLogout"
                                    name="logout"
                                    class="form-control"
                                    max="{{ date('Y-m-d') }}"
                                >

                            </div>

                        </div>


                        {{-- Reset --}}
                        <div class="col-md-2">

                            <button
                                type="button"
                                id="resetLogFilters"
                                class="btn btn-light"
                            >
                                Reset
                            </button>

                        </div>

                    </div>

                    </form>

                    <!-- Table -->
                    <div class="table-responsive">
                        <table id="logsTable" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Login</th>
                                    <th>Logout</th>
                                    <th>Device</th>
                                    <th>IP Address</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<script>
let logsTable;
document.addEventListener('DOMContentLoaded', function () {

    logsTable = $('#logsTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        ordering: true,
        responsive: true,

        ajax: {
            url: "{{ route('login-logs.index') }}",
            data: function (d) {
                d.user_id = $('#filterUser').val();
                d.role_id = $('#filterRole').val();   // fixed: was .val('')
                d.login = $('#filterLogin').val();
                d.logout = $('#filterLogout').val();
            }
        },

        columns: [
            { data: 'user', name: 'user.name', render: function (data) {
                if (!data) return `<span class="text-muted">Deleted User</span>`;
                return `<div>
                    <div class="font-weight-bold text-dark">${data.name ?? ''}</div>
                    <small class="text-muted">${data.email ?? ''}</small>
                </div>`;
            }},
            { data: 'role', name: 'role', render: d => d ? d.name : '—' },
            { data: 'login_at', name: 'login_at', render: function (data) {
                if (!data) return '—';
                const date = new Date(data);
                return `<div>${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                    <small class="text-muted">${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>`;
            }},
            { data: 'logout_at', name: 'logout_at', render: function (data) {
                if (!data) return `<span class="text-muted">—</span>`;
                const date = new Date(data);
                return `<div>${date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' })}</div>
                    <small class="text-muted">${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>`;
            }},
            { data: 'device', name: 'device', render: function (data) {
                if (data === 'desktop') return `<i class="mdi mdi-monitor mr-1"></i> Desktop`;
                if (data === 'tablet') return `<i class="mdi mdi-tablet mr-1"></i> Tablet`;
                if (data === 'mobile') return `<i class="mdi mdi-cellphone mr-1"></i> Mobile`;
                return data ? data.charAt(0).toUpperCase() + data.slice(1) : 'Unknown';
            }},
            { data: 'ip_address', name: 'ip_address', render: d => d ?? '—' },
            { data: 'location', name: 'location', render: function (data) {
                if (!data || !data.line) return '<span class="text-muted">—</span>';
                const flag = data.country_code
                    ? `<span class="fi fi-${data.country_code.toLowerCase()} mr-1"></span>`
                    : '';
                return `${flag}${data.line}`;
            }}
        ]
    });

    $('#filterUser, #filterRole, #filterLogin, #filterLogout').on('change', function () {
        logsTable.ajax.reload();
    });

    $('#resetLogFilters').on('click', function () {
        $('#filterUser, #filterRole').val('');
        $('#filterLogin, #filterLogout').val('');
        logsTable.ajax.reload();
    });

});

</script>
@endsection

