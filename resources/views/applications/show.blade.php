@extends('layout')

@section('title', 'Application Details')
@section('subtitle', 'View Application')

@section('content')

<style>
    .custom-card {
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 0.5px solid #e5e7eb;
        background: #fff;
    }

    .custom-header {
        background: #0d2c6c;
        color: #fff;
        font-weight: 500;
        font-size: 15px;
        border-radius: 14px 14px 0 0;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: .01em;
    }

    .icon-head {
        opacity: .75;
        font-size: 16px;
    }

    .detail-row {
        display: flex;
        align-items: flex-start;
        padding: 11px 0;
        border-bottom: 0.5px solid #f0f2f5;
        font-size: 13.5px;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-row i {
        width: 28px;
        color: #0d2c6c;
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .label {
        font-weight: 500;
        font-size: 12.5px;
        color: #6b7280;
        min-width: 190px;
        margin-right: 0;
        padding-top: 1px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 13px;
        border-radius: 20px;
        font-size: 12.5px;
        font-weight: 500;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .status-complete {
        background: #dcfce7;
        color: #166534;
    }
    .status-complete::before { background: #22c55e; }

    .status-progress {
        background: #fef3c7;
        color: #92400e;
    }
    .status-progress::before { background: #f59e0b; }

    .back-btn {
        border-radius: 8px;
        font-size: 13px;
        padding: 6px 16px;
        border: 0.5px solid #d1d5db;
    }
</style>

<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="mb-1" style="font-size:18px; font-weight:600; color:#111827;">
                            Lead Details
                        </h4>
                        <p class="mb-0" style="font-size:13px; color:#6b7280;">
                            View lead information
                        </p>
                    </div>

                    <div>
                        <a href="{{ route('applications.index') }}" class="btn btn-light back-btn">
                            <i class="mdi mdi-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>

                <div class="container-fluid mt-3">
                    <div class="row">

                        <!-- LEFT: Application Info -->
                        <div class="col-md-8 mb-4">
                            <div class="card custom-card h-100">

                                <div class="card-header custom-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="mdi mdi-file-document-outline menu-icon icon-head me-2"></i>
                                        {{ $application->company_business_name ?? 'Application' }}
                                    </div>
                                </div>

                                <div class="card-body">

                                    <div class="detail-row">
                                        <i class="mdi mdi-package-variant"></i>
                                        <span class="label">Product:</span>
                                        <span>{{ $application->product->name ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-office-building"></i>
                                        <span class="label">Company / Business Name:</span>
                                        <span>{{ $application->company_business_name ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-pound"></i>
                                        <span class="label">Company Number:</span>
                                        <span>{{ $application->company_number ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-domain"></i>
                                        <span class="label">Company Type:</span>
                                        <span>{{ $application->company_type ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-calendar"></i>
                                        <span class="label">Business Start Date:</span>
                                        <span>{{ $application->business_start_date?->format('d M Y') ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-account"></i>
                                        <span class="label">Customer Name:</span>
                                        <span>{{ $application->customer_name ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-account-tie"></i>
                                        <span class="label">Contact Person:</span>
                                        <span>{{ $application->contact_person ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-email"></i>
                                        <span class="label">Email:</span>
                                        <span>{{ $application->email ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-phone"></i>
                                        <span class="label">Phone:</span>
                                        <span>{{ $application->phone_no ? '+44 '.$application->phone_no : '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-map-marker"></i>
                                        <span class="label">Registered Address:</span>
                                        <span>{{ $application->business_registered_address ?? '-' }}</span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-map-marker-outline"></i>
                                        <span class="label">Trading Address:</span>
                                        <span>{{ $application->business_trading_address ?? '-' }}</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: Overview -->
                        <div class="col-md-4 mb-4">
                            <div class="card custom-card h-100">

                                <div class="card-header custom-header">
                                    <i class="mdi mdi-information-outline me-2 icon-head"></i>
                                    Lead Overview
                                </div>

                                <div class="card-body px-3 py-2">

                                    <div class="detail-row">
                                        <i class="mdi mdi-flag"></i>
                                        <span class="label">Status:</span>
                                        <span>
                                            @if($application->status === 'published')
                                                <span class="status-badge status-complete">Published</span>
                                            @else
                                                <span class="status-badge status-progress">Draft</span>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="detail-row">
                                        <i class="mdi mdi-package-variant"></i>
                                        <span class="label">Product:</span>
                                        <span>{{ $application->product->name ?? '-' }}</span>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
