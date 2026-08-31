@extends('layout')

@section('title', 'Dashboard')
@section('subtitle', 'Dashboard')

@section('content')

<style>
    .agency-header {
        padding-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
        margin-bottom: 24px;
    }

    .agency-label {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #6b7280;
        display: block;
        margin-bottom: 4px;
    }

    .agency-name {
        margin: 0;
        color: #111827;
        font-size: 2.2rem;
        font-weight: 600;
    }

    .stat-card {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f1f1f1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.25s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    }

    .stat-card-body {
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-label {
        font-size: 13px;
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 6px;
    }

    .stat-count {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .icon-circle i {
        font-size: 22px;
        color: #fff;
    }

    @media (max-width: 768px) {
        .stat-card-body {
            padding: 16px;
        }

        .stat-count {
            font-size: 24px;
        }
    }
</style>

{{-- Agency Name --}}
<div class="agency-header">
    <!-- <span class="agency-label">
        Agency
    </span> -->

    <h2 class="agency-name">
        AGILE ONE
    </h2>
</div>


{{-- Statistics --}}
<div class="row g-4">

    {{-- Total Members --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-body">

                <div>
                    <p class="stat-label">
                        Total Members
                    </p>

                    <h3 class="stat-count">
                        1
                    </h3>
                </div>

                <div class="icon-circle bg-primary">
                    <i class="mdi mdi-account-group"></i>
                </div>

            </div>
        </div>
    </div>


    {{-- Total Leads --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-body">

                <div>
                    <p class="stat-label">
                        Total Leads
                    </p>

                    <h3 class="stat-count">
                        0
                    </h3>
                </div>

                <div class="icon-circle bg-info">
                    <i class="mdi mdi-database"></i>
                </div>

            </div>
        </div>
    </div>


    {{-- Pending Leads --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-body">

                <div>
                    <p class="stat-label">
                        Pending Leads
                    </p>

                    <h3 class="stat-count">
                        0
                    </h3>
                </div>

                <div class="icon-circle bg-warning">
                    <i class="mdi mdi-timer-sand"></i>
                </div>

            </div>
        </div>
    </div>


    {{-- Completed Leads --}}
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="stat-card">
            <div class="stat-card-body">

                <div>
                    <p class="stat-label">
                        Completed Leads
                    </p>

                    <h3 class="stat-count">
                        0
                    </h3>
                </div>

                <div class="icon-circle bg-success">
                    <i class="mdi mdi-check-circle"></i>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection
