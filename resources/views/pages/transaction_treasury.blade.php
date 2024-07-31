@extends('layouts.backend')

@section('css')

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

        .dt-search{
            margin-bottom: 10px;
        }
    </style>
@endsection


@section('content-title', 'GCash Transaction')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <input type="hidden" id="currentDate" value="{{ Carbon\Carbon::now()->format('m-d-Y') }}">
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

    @include('modals.transaction_list_modal_treasury')

@endsection




@section('js')

    <script src="{{ asset('js/plugins/datatables-buttons/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    {{-- <script src="{{ asset('js/plugins/datatables-buttons-jszip/jszip.min.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-buttons/buttons.html5.min.js') }}"></script> --}}

    <script>
        $(function() {

            const path = $("#path").val();

            const table = $("#table").DataTable({
                processing: true,
                serverSide: false,
                searchable: true,
                pagination: true,
                destroy: true,
                ajax: {
                    type: 'get',
                    url: '{{ route('fetch_transactions') }}',
                    data: {
                        path,
                        _token: '{{ csrf_token() }}'
                    }
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

                setTimeout(() => {
                    const modalTable = $("#modalTable").DataTable({
                        processing: true,
                        serverSide: false,
                        destroy: true,
                        ajax: {
                            type: 'get',
                            url: '{{ route('fetch_transactions_approved') }}',
                            data: {
                                transacNum,
                                status,
                                _token: '{{ csrf_token() }}'
                            }

                        },
                        columns: [{
                                data: 'branch_name'
                            },
                            {
                                data: 'gcash_number'
                            },
                            {
                                data: 'gcash_name', width: '30%'
                            },
                            {
                                data: 'amount'
                            },
                            {
                                data: 'remarks'
                            },
                        ],
                        layout: {
                            topStart: {
                                buttons: [ 'csv', 'excel',]
                            }
                        },
                        scrollX: true,
                        drawCallback: function() {
                            if (status) {
                                $(".downloadBtn").prop('disabled', true);
                            }
                        }
                    });
                }, 200);
            })


            // const table = $("#table").DataTable({
            //     processing: true,
            //     serverSide: false,
            //     searchable: true,
            //     pagination: true,
            //     destroy: true,
            //     ajax: {
            //         type: 'get',
            //         url: '{{ route('fetch_transactions_approved') }}'
            //     },
            //     columns: [{
            //             data: 'mobile_number'
            //         },
            //         {
            //             data: 'client_name'
            //         },
            //         {
            //             data: 'pension_type'
            //         },
            //         {
            //             data: 'pension_number'
            //         },
            //         {
            //             data: 'amount'
            //         },
            //     ],
            //     dom: 'Bfrtip',
            //     buttons: [{
            //         text: '<i class="fa fa-download me-1"></i> Download',
            //         className: 'bg-primary',
            //         action: function(e, dt,) {
            //             const currentDate = $("#currentDate").val();
            //             const copiedData = dt.buttons.exportData().body;
            //             const dataString = copiedData.map(row => row.join('\t')).join('\n');
            //             const blob = new Blob([dataString], {
            //                 type: 'text/plain'
            //             });
            //             const a = document.createElement('a');
            //             a.href = URL.createObjectURL(blob);
            //             a.download = currentDate + '.txt';
            //             a.click();
            //             URL.revokeObjectURL(a.href);
            //         }
            //     }, ]
            // });
        })
    </script>
@endsection
