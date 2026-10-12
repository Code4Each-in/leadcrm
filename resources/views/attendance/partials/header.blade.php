{{-- Page header. Expects $heading and $description. Admin/Super Admin
     also get the My Attendance / All Employees switch (the All Employees
     routes are behind the 'admin' middleware too). --}}
<div class="att-page-header">
    <div>
        <div class="att-eyebrow">Attendance</div>
        <h4 class="card-title mb-1">{{ $heading }}</h4>
        <p>{{ $description }}</p>
    </div>

    @if (auth()->user()->isAdminOrAbove())
        <div class="att-tabs">
            <a href="{{ route('attendance.index') }}"
               class="att-tab {{ request()->routeIs('attendance.index') ? 'active' : '' }}">
                <i class="mdi mdi-account-clock-outline"></i>
                My Attendance
            </a>
            <a href="{{ route('attendance.admin') }}"
               class="att-tab {{ request()->routeIs('attendance.admin') ? 'active' : '' }}">
                <i class="mdi mdi-account-group-outline"></i>
                All Employees
            </a>
        </div>
    @endif
</div>
