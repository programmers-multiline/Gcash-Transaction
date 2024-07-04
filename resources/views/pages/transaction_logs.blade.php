@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
@endsection

@section('content-title', 'Approved Transactions')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table"
                    class="table js-table-checkable fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
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

    @include('modals.transaction_list_modal')

@endsection




@section('js')


    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>

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
@endsection
