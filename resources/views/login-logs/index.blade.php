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
                                        name="user_id"
                                        class="form-control"
                                    >

                                        <option value="">
                                            All Users
                                        </option>

                                        @foreach($users as $user)

                                            <option
                                                value="{{ $user->id }}"
                                                {{ request('user_id') == $user->id ? 'selected' : '' }}
                                            >
                                                {{ $user->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                            </div>


                            {{-- Login --}}
                            <div class="col-md-3">

                                <div class="form-group mb-0">

                                    <label class="font-weight-bold">
                                        Login
                                    </label>

                                    <input
                                        type="date"
                                        name="login"
                                        class="form-control"
                                        value="{{ request('login') }}"
                                    >

                                </div>

                            </div>


                            {{-- Logout --}}
                            <div class="col-md-3">

                                <div class="form-group mb-0">

                                    <label class="font-weight-bold">
                                        Logout
                                    </label>

                                    <input
                                        type="date"
                                        name="logout"
                                        class="form-control"
                                        value="{{ request('logout') }}"
                                    >

                                </div>

                            </div>


                            {{-- Buttons --}}
                            <div class="col-md-3">

                                <button
                                    type="submit"
                                    class="btn btn-primary mr-2"
                                >
                                    <i class="mdi mdi-filter-outline"></i>
                                    Filter
                                </button>

                                <a
                                    href="{{ route('login-logs.index') }}"
                                    class="btn btn-light"
                                >
                                    Reset
                                </a>

                            </div>

                        </div>

                    </form>


                    {{-- Table --}}
                    <div class="table-responsive">

                        <table class="table table-hover">

                            <thead>

                                <tr>

                                    <th>User</th>

                                    <th>Role</th>

                                    <th>Login</th>

                                    <th>Logout</th>

                                    <th>Device</th>

                                    <th>IP Address</th>

                                    <th>Status</th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($logs as $log)

                                    <tr>

                                        {{-- User --}}
                                        <td>

                                            @if($log->user)

                                                <div class="font-weight-bold text-dark">
                                                    {{ $log->user->name }}
                                                </div>

                                                <small class="text-muted">
                                                    {{ $log->user->email }}
                                                </small>

                                            @else

                                                <span class="text-muted">
                                                    Deleted User
                                                </span>

                                            @endif

                                        </td>


                                        {{-- Role --}}
                                        <td>

                                            @if($log->user && $log->user->role)

                                                {{ $log->user->role->name }}

                                            @else

                                                —

                                            @endif

                                        </td>


                                        {{-- Login --}}
                                        <td>

                                            @if($log->login_at)

                                                <div>
                                                    {{ $log->login_at->format('d M Y') }}
                                                </div>

                                                <small class="text-muted">
                                                    {{ $log->login_at->format('h:i A') }}
                                                </small>

                                            @else

                                                —

                                            @endif

                                        </td>


                                        {{-- Logout --}}
                                        <td>

                                            @if($log->logout_at)

                                                <div>
                                                    {{ $log->logout_at->format('d M Y') }}
                                                </div>

                                                <small class="text-muted">
                                                    {{ $log->logout_at->format('h:i A') }}
                                                </small>

                                            @else

                                                —

                                            @endif

                                        </td>


                                        {{-- Device --}}
                                        <td>

                                            @if($log->device === 'desktop')

                                                <i class="mdi mdi-monitor mr-1"></i>
                                                Desktop

                                            @elseif($log->device === 'tablet')

                                                <i class="mdi mdi-tablet mr-1"></i>
                                                Tablet

                                            @elseif($log->device === 'mobile')

                                                <i class="mdi mdi-cellphone mr-1"></i>
                                                Mobile

                                            @else

                                                {{ ucfirst($log->device ?? 'Unknown') }}

                                            @endif

                                        </td>


                                        {{-- IP Address --}}
                                        <td>
                                            {{ $log->ip_address ?? '—' }}
                                        </td>


                                        {{-- Status --}}
                                        <td>

                                            @if($log->logout_at)

                                                <span class="badge badge-secondary">
                                                    Logged Out
                                                </span>

                                            @else

                                                <span class="badge badge-success">
                                                    Active
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="7"
                                            class="text-center py-5"
                                        >

                                            <i
                                                class="mdi mdi-login-variant text-muted"
                                                style="font-size: 35px;"
                                            ></i>

                                            <p class="text-muted mt-2 mb-0">
                                                No login logs found.
                                            </p>

                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>


                    {{-- Pagination --}}
                    @if($logs->hasPages())

                        <div class="mt-4">

                            {{ $logs->links() }}

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
