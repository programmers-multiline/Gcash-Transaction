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
    <!-- Page Content -->
    <div class="loader-container" id="loader"
        style="display: none; width: 100%; height: 100%; position: absolute; top: 0; right: 0; margin-top: 0; background-color: rgba(0, 0, 0, 0.26); z-index: 2033;">
        <dotlottie-player src="{{ asset('js/loader(new).json') }}" background="transparent" speed="1"
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 160px; height: 160px" direction="1" playMode="normal"
            loop autoplay>Loading</dotlottie-player>
    </div>
    <div class="content">
        <div id="tableContainer" class="block block-rounded">
            <div class="block-content block-content-full overflow-x-auto">
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

    @include('modals.transaction_list_modal_acc')


@endsection




@section('js')


    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    {{-- filepond --}}
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
                            url: '{{ route('fetch_transaction_modal') }}',
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
                            },
                            {
                                data: 'branch_name'
                            },
                            {
                                data: 'mobile_number'
                            },
                            {
                                data: 'client_name'
                            },
                            {
                                data: 'amount'
                            },
                            {
                                data: 'remarks'
                            },
                            {
                                data: 'status'
                            },
                        ],
                        select: {
                            style: 'multi+shift',
                            selector: 'td'
                        },
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
                                        modalTable.ajax.reload()
                                        $("#modalTableAuto").DataTable()
                                            .ajax.reload();
                                    }
                                })
                            });

                            $('[data-toggle="tooltip"]').tooltip();
                        }
                    });
                    $(".click").click();
                    $(".click").click();
                    $(".click").click();
                    modalTable.select.selector('td:first-child');

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
                            url: '{{ route('upload_transaction_acc') }}',
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
                                data: 'amount'
                            },
                            {
                                data: 'acc_mn'
                            },
                            {
                                data: 'acc_cn'
                            },
                            {
                                data: 'acc_amount'
                            },
                            {
                                data: 'upload_status'
                            },
                            {
                                data: 'status',
                                className: 'state',
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


                // $(".test").click()

            });

            let data;

            $(document).on("change", ".selectedTools", function() {

                data = $("#modalTable").DataTable().rows({
                    selected: true
                }).data();

            })


            $(document).on("click", "#approveBtn", function() {

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

                const transactionCountAcc = parseInt($("#transactionCountAcc").text());

                $.ajax({
                    url: '{{ route('approve_transaction') }}',
                    method: 'post',
                    data: {
                        idArray: arrayToString,
                        _token: "{{ csrf_token() }}"
                    },
                    beforeSend() {
                        $("#loader").show();
                    },
                    success(response) {
                        const transactionStatus = $.map(response, function(res) {
                            return res.transaction_status;
                        });

                        const pending = transactionStatus.includes("pending")

                        if (!pending) {
                            if (transactionCountAcc == 1) {
                                $(".countContainer").addClass("d-none")
                                $(".countIndicator").addClass("d-none")
                            } else {
                                $("#transactionCountAcc").text(transactionCountAcc - 1);
                            }
                        }
                        $("#loader").hide();
                        table.ajax.reload();
                        modalTable.ajax.reload();
                        $("#modalTableAuto").DataTable().ajax.reload();
                        showToast("success", "Transaction Approved Success");
                    }
                })
            })

            $(document).on("click", "#declineBtn", function() {

                const transactionCountAcc = parseInt($("#transactionCountAcc").text());

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

                $.ajax({
                    url: '{{ route('decline_transaction') }}',
                    method: 'post',
                    data: {
                        idArray: arrayToString,
                        _token: "{{ csrf_token() }}"
                    },
                    success(response) {
                        const transactionStatus = $.map(response, function(res) {
                            return res.transaction_status;
                        });

                        const pending = transactionStatus.includes("pending")

                        if (!pending) {
                            if (transactionCountAcc == 1) {
                                $(".countContainer").addClass("d-none")
                                $(".countIndicator").addClass("d-none")
                            } else {
                                $("#transactionCountAcc").text(transactionCountAcc - 1);
                            }
                        }
                        table.ajax.reload();
                        modalTable.ajax.reload();
                        $("#modalTableAuto").DataTable().ajax.reload();
                        showToast("success", "Transaction Declined Success");
                    }
                })
            })




        })
    </script>

    <script src="{{ asset('js\lib\fileupload.js') }}"></script>
    <script>
        $(function() {
            $("#approveBtnAuto").click(function() {
                const data = $("#modalTableAuto").DataTable().rows().data();
                const transactionCountAcc = parseInt($("#transactionCountAcc").text());

                const ids = [];
                for (let i = 0; i < data.length; i++) {
                    if (data[i].acc_mn && data[i].mobile_number) {
                        ids.push(data[i].id);
                    }
                }

                const arrayToString = JSON.stringify(ids);

                $.ajax({
                    url: '{{ route('approve_transaction') }}',
                    method: "post",
                    data: {
                        idArray: arrayToString,
                        _token: "{{ csrf_token() }}",
                    },
                    success(response) {

                        const transactionStatus = $.map(response, function(res) {
                            return res.transaction_status;
                        });

                        const pending = transactionStatus.includes("pending")

                        if (!pending) {
                            if (transactionCountAcc == 1) {
                                $(".countContainer").addClass("d-none")
                                $(".countIndicator").addClass("d-none")
                            } else {
                                $("#transactionCountAcc").text(transactionCountAcc - 1);
                            }
                        }

                        $("#table").DataTable().ajax.reload();
                        $("#modalTable").DataTable().ajax.reload();
                        showToast("success", "Transaction Approved Success");

                        $(".state span").each(function() {
                            if ($(this).text() == 'pending') {
                                $(this).text('approved')
                                $(this).removeClass('bg-warning')
                                $(this).addClass('bg-success')
                            }
                        })
                    },
                });
            });
        })
    </script>
@endsection
