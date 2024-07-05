@extends('layouts.backend')

@section('content')
    @php
        $user_type_id = Auth::user()->user_type_id;
    @endphp
    <!-- Page Content -->
    <div class="content">
        @if ($user_type_id == 2)
            <div class="row">
                <!-- Row #1 -->
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-earth">{{ $uploads }}</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Transactions Uploads</div>
                            </div>
                            <div class="text-end">
                                <i class="fa fa-upload fa-2x text-earth-light"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-primary">{{ $approved_transactions }}</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Approved Transactions</div>
                            </div>
                            <div class="text-end">
                                <i class="fa fa-circle-check fa-2x text-primary-light"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-pulse">{{ $declined_transactions }}</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Declined Transactions</div>
                            </div>
                            <div class="text-end">
                                <i class="fa fa-circle-xmark fa-2x text-pulse-light"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- END Row #1 -->
            </div>
        @endif
        @if ( $user_type_id == 3 || $user_type_id == 5)
            <div class="row">
                <!-- Row #1 -->
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-primary">{{ $approved_transactions }}</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Approved Transactions</div>
                            </div>
                            <div class="text-end">
                                <i class="fa fa-circle-check fa-2x text-primary-light"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-pulse">{{ $declined_transactions }}</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Declined Transactions</div>
                            </div>
                            <div class="text-end">
                                <i class="fa fa-circle-xmark fa-2x text-pulse-light"></i>
                            </div>
                        </div>
                    </a>
                </div>
                {{-- <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-elegance">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Pull Out Request</div>
                            </div>
                            <div class="text-end">
                                <i class="si si-action-undo fa-2x text-elegance-light"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-corporate">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Request for DAF</div>
                            </div>
                            <div class="text-end">
                                <i class="si si-envelope fa-2x text-corporate-light"></i>
                            </div>
                        </div>
                    </a>
                </div> --}}
                <!-- END Row #1 -->
            </div>
            <!-- END Page Content -->
            @endif
        @endsection
