@extends('layouts.backend')

@section('content')
    @php
        $user_type_id = Auth::user()->user_type_id;
    @endphp
    <!-- Page Content -->
    <div class="content d-none">
        @if ($user_type_id == 2)
            <div class="row">
                <!-- Row #1 -->
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <i class="si si-bag fa-2x text-primary-light"></i>
                            </div>
                            <div class="text-end">
                                <div class="fs-3 fw-semibold text-primary">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Tools and Equipments in
                                    Warehouse</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <i class="si si-bag fa-2x text-primary-light"></i>
                            </div>
                            <div class="text-end">
                                <div class="fs-3 fw-semibold text-primary">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Transferred Tools and
                                    Equipment</div>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <i class="si si-wallet fa-2x text-earth-light"></i>
                            </div>
                            <div class="text-end">
                                <div class="fs-3 fw-semibold text-earth">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Pending RTTTE</div>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <i class="si si-users fa-2x text-pulse"></i>
                            </div>
                            <div class="text-end">
                                <div class="fs-3 fw-semibold text-pulse">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total RTTTE for Approval</div>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- END Row #1 -->
            </div>
        @endif
        @if ( $user_type_id == 3 || $user_type_id == 4 || $user_type_id == 5)
            <div class="row">
                <!-- Row #1 -->
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-earth">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total TEIS</div>
                            </div>
                            <div class="text-end">
                                <i class="si si-doc fa-2x text-earth-light"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
                    <a class="block block-rounded block-bordered block-link-shadow" href="javascript:void(0)">
                        <div class="block-content block-content-full d-sm-flex justify-content-between align-items-center">
                            <div class="d-none d-sm-block">
                                <div class="fs-3 fw-semibold text-primary">0</div>
                                <div class="fs-xs fw-semibold text-uppercase text-muted">Total Pending RFTEIS</div>
                            </div>
                            <div class="text-end">
                                <i class="si si-close fa-2x text-primary-light"></i>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-xl-3">
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
                </div>
                <!-- END Row #1 -->
            </div>
            <!-- END Page Content -->
            @endif
        @endsection
