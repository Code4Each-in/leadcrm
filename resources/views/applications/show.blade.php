@extends('layout')

@section('title', 'Application Details')
@section('subtitle', 'View Application')

@section('content')

<div class="row">

    <div class="col-12 grid-margin stretch-card">

        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h4 class="card-title mb-1">
                            Lead Details
                        </h4>

                        <p class="card-description mb-0">
                            View lead information
                        </p>

                    </div>

                    <div>
                        <a
                            href="{{ route('applications.index') }}"
                            class="btn btn-light"
                        >
                            Back
                        </a>

                    </div>

                </div>


                <div class="row">

                    <div class="col-md-6 mb-4">

                        <label>Product</label>

                        <div class="detail-value">
                            {{ $application->product->name ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Status</label>

                        <div class="detail-value">

                            @if($application->status === 'published')

                                <span class="badge badge-success">
                                    Published
                                </span>

                            @else

                                <span class="badge badge-warning">
                                    Draft
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Company / Business Name</label>

                        <div class="detail-value">
                            {{ $application->company_business_name ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Company Number</label>

                        <div class="detail-value">
                            {{ $application->company_number ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Company Type</label>

                        <div class="detail-value">
                            {{ $application->company_type ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Business Start Date</label>

                        <div class="detail-value">
                            {{ $application->business_start_date?->format('d M Y') ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Customer Name</label>

                        <div class="detail-value">
                            {{ $application->customer_name ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Contact Person</label>

                        <div class="detail-value">
                            {{ $application->contact_person ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Email</label>

                        <div class="detail-value">
                            {{ $application->email ?? '-' }}
                        </div>

                    </div>


                    <div class="col-md-6 mb-4">

                        <label>Phone</label>

                        <div class="detail-value">
                            {{ $application->phone_no ? '+44 '.$application->phone_no : '-' }}
                        </div>

                    </div>


                    <div class="col-12 mb-4">

                        <label>Registered Address</label>

                        <div class="detail-value">
                            {{ $application->business_registered_address ?? '-' }}
                        </div>

                    </div>


                    <div class="col-12 mb-4">

                        <label>Trading Address</label>

                        <div class="detail-value">
                            {{ $application->business_trading_address ?? '-' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<style>

    .detail-value {
        margin-top: 6px;
        padding: 12px 14px;
        min-height: 44px;
        background: #fbfbfd;
        border: 1px solid #e2e6ee;
        border-radius: 8px;
        color: #3e4b5b;
    }

    .card-body label {
        font-weight: 500;
        font-size: 0.85rem;
        color: #3e4b5b;
    }

    .badge {
        padding: 0.45rem 0.7rem;
        border-radius: 6px;
        font-size: 0.72rem;
    }

</style>

@endsection
