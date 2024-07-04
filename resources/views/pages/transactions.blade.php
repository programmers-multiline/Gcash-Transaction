@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/filepond/filepond.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css') }}">
    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order,
        {
        display: none;
        }

        #table>thead>tr>th.dt-orderable-asc.dt-orderable-desc.dt-type-numeric>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-asc.dt-orderable-desc.dt-type-numeric.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        .filepond--credits {
            display: none;
        }
    </style>
@endsection

@section('content-title', 'GCash Transaction')

@section('content')
    <div class="loader-container" id="loader"
        style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 1033;">
        <dotlottie-player src="{{ asset('js/loader.json') }}" background="transparent" speed="1"
            style=" position: absolute; top: 35%; left: 45%; width: 160px; height: 160px" direction="1" playMode="normal"
            loop autoplay>Loading</dotlottie-player>
    </div>
    <!-- Page Content -->
    <div class="content">
        <button type="button" class="btn btn-success mb-3 d-block ms-auto" data-bs-toggle="modal"
            data-bs-target="#uploadTransaction"><i class="fa fa-upload me-1"></i>Upload Transaction</button>
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>Transaction Number</th>
                            <th>Date Uploaded</th>
                            <th>Created By</th>
                            <th>Total Approved</th>
                            <th>Total Declined</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END Page Content -->










    {{-- modal upload transaction --}}

    <div class="modal fade" id="uploadTransaction" tabindex="-1" role="dialog" aria-labelledby="modal-popin"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-fromleft" role="document">
            <div class="modal-content">
                <form id="uploadTransactionForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="routeUrl" value="{{ route('upload_transaction') }}">
                    <div class="block block-rounded shadow-none mb-0">
                        <div class="block-header block-header-default">
                            <h3 class="block-title">Upload Transaction</h3>
                            <div class="block-options">
                                <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <div class="block-content fs-sm">
                            <div class="block block-rounded">
                                <div class="block-content">
                                    <div class="mb-4">
                                        <label class="form-label" for="transactionUpload">Upload Transaction Here.</label>
                                        <input class="form-control" type="file" name='importTransaction'
                                            id="transactionUpload">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="block-content block-content-full block-content-sm text-end border-top">
                            <button type="button" id="closeModal" class="btn btn-alt-secondary" data-bs-dismiss="modal">
                                Close
                            </button>
                            <button type="submit" class="btn btn-alt-primary">
                                Upload
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @include('modals.transaction_list_modal')

@endsection




@section('js')


    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    <script src="{{ asset('js/plugins/filepond/filepond.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js') }}"></script>
    <script
        src="{{ asset('js/plugins/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-edit/filepond-plugin-image-edit.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js') }}">
    </script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-crop/filepond-plugin-image-crop.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-resize/filepond-plugin-image-resize.min.js') }}"></script>
    <script src="{{ asset('js/plugins/filepond-plugin-image-transform/filepond-plugin-image-transform.min.js') }}">
    </script>
    </script>

    {{-- filepond --}}
    <script>
        $(function() {
            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                searchable: true,
                pagination: true,
                destroy: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_transactions') }}'
                },
                columns: [{
                        data: 'view_transaction_lists'
                    },
                    {
                        data: 'transaction_number'
                    },
                    {
                        data: 'date_uploaded'
                    },
                    {
                        data: function(row) {
                            return row.fn + ' ' + row.ln;
                        }
                    },
                    {
                        data: 'total_number_approved'
                    },
                    {
                        data: 'total_number_declined'
                    },
                    {
                        data: 'status'
                    },
                ],
            });

            $(document).on('click', '.viewTransaction', function() {
                const transacNum = $(this).data("tn");
                const status = $(this).data("status");


                const modalTable = $("#modalTable").DataTable({
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    ajax: {
                        type: 'get',
                        url: '{{ route('fetch_transaction_modal') }}',
                        data: {
                            transacNum,
                            status,
                            _token: '{{ csrf_token() }}'
                        }

                    },
                    columns: [{
                            data: 'mobile_number'
                        },
                        {
                            data: 'client_name'
                        },
                        {
                            data: 'pension_type'
                        },
                        {
                            data: 'pension_number'
                        },
                        {
                            data: 'amount'
                        },
                        {
                            data: 'status'
                        },
                    ],
                    scrollX: true,
                    drawCallback: function() {
                        $(".receivedBtn").tooltip();
                    }
                });
            })
        })
    </script>
    <script src="{{ asset('js\lib\fileupload.js') }}"></script>
@endsection
