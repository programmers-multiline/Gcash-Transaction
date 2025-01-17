@extends('layouts.backend')

@section('css')
    <link rel="stylesheet" href="{{ asset('js/plugins/datatables-select/css/select.dataTables.css') }}">
    <style>
        #table>thead>tr>th.text-center.dt-orderable-none.dt-ordering-asc>span.dt-column-order {
            display: none;
        }

        #table>thead>tr>th.dt-orderable-none.dt-select.dt-ordering-asc>span.dt-column-order,
        {
        display: none;
        }

        #table>thead>tr:nth-child(1)>.dt-orderable-asc.dt-orderable-desc>span.dt-column-order {
            display: none;
        }
    </style>
@endsection


@section('content-title', 'GCash Transaction')

@section('content')
    <!-- Page Content -->
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
                <!-- DataTables functionality is initialized with .js-dataTable-responsive class in js/pages/be_tables_datatables.min.js which was auto compiled from _js/pages/be_tables_datatables.js -->
                <table id="table" class="table fs-sm table-bordered hover table-vcenter js-dataTable-responsive">
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

    @include('modals.transaction_list_modal_approver')

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
                    url: '{{ route('fetch_transactions_approver') }}',
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

                if (status) {
                    $("#approveBtn").prop('disabled', true);
                    $("#declineBtn").prop('disabled', true);
                } else {
                    $("#approveBtn").prop('disabled', false);
                    $("#declineBtn").prop('disabled', false);
                }

                setTimeout(() => {
                    const modalTable = $("#modalTable").DataTable({
                        processing: true,
                        serverSide: false,
                        searchable: true,
                        pagination: true,
                        orderable: false,
                        order: false,
                        destroy: true,
                        "aoColumnDefs": [{
                                "bSortable": false,
                                "aTargets": [0]
                            },
                            {
                                "targets": [1],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        ajax: {
                            type: 'get',
                            url: '{{ route('fetch_transactions_approver_modal') }}',
                            data: {
                                transacNum,
                                status,
                                _token: '{{ csrf_token() }}'
                            }
                        },
                        columns: [{
                                data: null,
                                render: DataTable.render.select(),
                                className: 'selectedTools'
                            },
                            {
                                data: 'id'
                            }, {
                                data: 'mobile_number'
                            },
                            {
                                data: 'client_name'
                            },
                            {
                                data: 'acc_mn'
                            },
                            {
                                data: 'acc_cn'
                            },
                            {
                                data: 'status'
                            },
                            {
                                data: 'approver_status'
                            }
                        ],
                        select: {
                            style: 'multi+shift',
                            selector: 'td'
                        },
                    });
                    modalTable.select.selector('td:first-child');

                    // $(".test").click()

                    modalTable.on('select', function(e, dt, type, indexes) {
                        if (type === 'row') {
                            var rows = modalTable.rows(indexes).nodes().to$();
                            $.each(rows, function() {
                                if ($(this).hasClass('bg-gray')) {
                                    modalTable.row($(this)).deselect();
                                    showToast("error",
                                        "Cannot select declined transaction!");
                                }
                            })
                        }
                    });

                    const transactionNumber = $("#transactionNumber").val(transacNum)

                    const modalTableAuto = $("#modalTableAuto").DataTable({
                        processing: true,
                        serverSide: false,
                        searchable: true,
                        pagination: true,
                        destroy: true,
                        "aoColumnDefs": [{
                                "bSortable": false,
                                "aTargets": [0]
                            },
                            {
                                "targets": [0],
                                "visible": false,
                                "searchable": false
                            }
                        ],
                        ajax: {
                            type: 'post',
                            url: '{{ route('upload_transaction_approver') }}',
                            data: {
                                transacNum,
                                status,
                                _token: '{{ csrf_token() }}'
                            }
                        },
                        columns: [{
                                data: 'id'
                            },
                            {
                                data: 'mobile_number'
                            },
                            {
                                data: 'client_name'
                            },
                            {
                                data: 'acc_mn'
                            },
                            {
                                data: 'acc_cn'
                            },
                            {
                                data: 'acc_status'
                            },
                            {
                                data: 'approver_mn'
                            },
                            {
                                data: 'approver_cn'
                            },
                            {
                                data: 'upload_status'
                            },
                            {
                                data: 'approver_status'
                            },
                        ],
                        drawCallback: function() {
                            $(".undoStatus").click(function() {
                                const id = $(this).data('id')

                                $.ajax({
                                    url: '{{ route('revert_status') }}',
                                    method: 'post',
                                    data: {
                                        id,
                                        _token: '{{ csrf_token() }}',
                                    },
                                    success() {
                                        modalTableAuto.ajax.reload()
                                    }
                                })
                            });
                        }
                    });

                }, 200);


            });

            let data;

            $(document).on("change", ".selectedTools", function() {

                data = $("#modalTable").DataTable().rows({
                    selected: true
                }).data();

            })


            $(document).on("click", "#approveBtn", function() {

                const path = $("#path").val();

                data = $("#modalTable").DataTable().rows({
                    selected: true
                }).data();

                if (data.length == 0) {
                    showToast("error", "Select Item first!");
                    return;
                }

                const ids = [];

                for (var i = 0; i < data.length; i++) {

                    const id = data[i].id

                    ids.push(id)
                }


                const arrayToString = JSON.stringify(ids);

                const table = $("#table").DataTable();
                const modalTable = $("#modalTable").DataTable();

                const transactionCountApprover = parseInt($("#transactionCountApprover").text());

                $.ajax({
                    url: '{{ route('approve_transaction') }}',
                    method: 'post',
                    data: {
                        path,
                        idArray: arrayToString,
                        _token: "{{ csrf_token() }}"
                    },
                    success(response) {


                        const pending = response.includes(null)


                        if (!pending) {
                            if (transactionCountApprover == 1) {
                                $(".countContainer").addClass("d-none")
                                $(".countIndicator").addClass("d-none")
                            } else {
                                $("#transactionCountApprover").text(transactionCountApprover - 1);
                            }
                        }

                        table.ajax.reload();
                        modalTable.ajax.reload();
                        showToast("success", "Transaction Approved Success");
                    }
                })
            })

            $(document).on("click", "#declineBtn", function() {

                const path = $("#path").val();

                data = $("#modalTable").DataTable().rows({
                    selected: true
                }).data();

                if (data.length == 0) {
                    showToast("error", "Select Item first!");
                    return;
                }

                const ids = [];

                for (var i = 0; i < data.length; i++) {

                    const id = data[i].id

                    ids.push(id)
                }


                const arrayToString = JSON.stringify(ids);

                const table = $("#table").DataTable();
                const modalTable = $("#modalTable").DataTable();

                const transactionCountApprover = parseInt($("#transactionCountApprover").text());

                $.ajax({
                    url: '{{ route('decline_transaction') }}',
                    method: 'post',
                    data: {
                        path,
                        idArray: arrayToString,
                        _token: "{{ csrf_token() }}"
                    },
                    success(response) {

                        const pending = response.includes(null)


                        if (!pending) {
                            if (transactionCountApprover == 1) {
                                $(".countContainer").addClass("d-none")
                                $(".countIndicator").addClass("d-none")
                            } else {
                                $("#transactionCountApprover").text(transactionCountApprover - 1);
                            }
                        }

                        table.ajax.reload();
                        modalTable.ajax.reload();
                        showToast("success", "Transaction Declined Success");
                    }
                })
            })
        })
    </script>   
    <script src="{{ asset('js\lib\fileupload.js') }}"></script>
    <script>
         $("#approveBtnAuto").click(function() {
                const path = $("#path").val();
                const data = $("#modalTableAuto").DataTable().rows().data();
                const transactionCountApprover = parseInt($("#transactionCountApprover").text());

                const ids = [];
                for (let i = 0; i < data.length; i++) {
                    if (data[i].acc_mn && data[i].mobile_number && data[i].approver_mn) {
                        ids.push(data[i].id);
                    }
                }

                const arrayToString = JSON.stringify(ids);

                $.ajax({
                    url: '{{ route('approve_transaction') }}',
                    method: "post",
                    data: {
                        idArray: arrayToString,
                        path,
                        _token: "{{ csrf_token() }}",
                    },
                    success(response) {

                        // const transactionStatus = $.map(response, function(res) {
                        //     return res.transaction_status;
                        // });

                        const pending = response.includes("pending")

                        if (!pending) {
                            if (transactionCountApprover == 1) {
                                $(".countContainer").addClass("d-none")
                                $(".countIndicator").addClass("d-none")
                            } else {
                                $("#transactionCountApprover").text(transactionCountApprover - 1);
                            }
                        }

                        $("#table").DataTable().ajax.reload();
                        $("#modalTable").DataTable().ajax.reload();
                        showToast("success", "Transaction Approved Success");

                        $(".uploadStatus").each(function() {
                            if ($(this).text() == 'pending') {
                                $(this).text('approved')
                                $(this).removeClass('bg-warning')
                                $(this).addClass('bg-success')
                            }
                        })
                    },
                });
            });
    </script>
@endsection
