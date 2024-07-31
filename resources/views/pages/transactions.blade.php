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

        .modal-backdrop.show {
        z-index: 1040 !important;

        }
        #transactionListModalEdit {
            z-index: 3056 !important;
        }
        #transactionListModalEdit .modal-backdrop {
            background: #ff7474;
        }
    </style>
@endsection

@section('content-title', 'GCash Transactions')

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
            data-bs-target="#uploadTransaction"><i class="fa fa-upload me-1"></i>Upload Transactions</button>
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










    {{-- modal upload transaction --}}

    <div class="modal fade" id="uploadTransaction" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
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
                                        <label class="form-label" for="transactionUpload">Upload Transactions Here.</label>
                                        <input class="form-control" type="file" name='importTransaction'
                                            accept=".csv, .xlsx, .xls" id="transactionUpload">
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


    {{-- Edit transaction modal --}}

    <div class="modal fade" id="transactionListModalEdit" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="block block-rounded shadow mb-0 mt-5">
                    <div class="block-header block-header-default bg-success">
                        <input type="hidden" id="path" value="{{ request()->path() }}">
                        <h3 class="block-title text-light">EDIT</h3>
                        <div class="block-options">
                            <button type="button" class="btn-block-option text-light closeModalEdit" data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="block-content fs-sm">
                        <form action="be_forms_premade.html" method="POST" onsubmit="return false;">
                            <input type="hidden" id="hiddenTransactionId">
                            <div class="col-12 mb-4">
                                <label class="form-label" for="clientName">Client Name</label>
                                <input type="text" class="form-control" id="clientName"
                                    name="clientName" placeholder="Client Name">
                            </div>
                            <div class="row mb-4">
                                <div class="col-6">
                                    <label class="form-label" for="mobileNumber">Mobile Number</label>
                                    <input type="text" class="form-control" id="mobileNumber"
                                        name="mobileNumber" placeholder="Mobile Number">
                                </div>
                                <div class="col-6">
                                    <label class="form-label" for="amount">Amount</label>
                                    <input type="text" class="form-control" id="amount"
                                        name="amount" placeholder="Amount">
                                </div>
                            </div>
                            {{-- <div class="mb-4">
                                <label class="form-label" for="contact1-subject">Where?</label>
                                <select class="form-select" id="contact1-subject" name="contact1-subject"
                                    size="1">
                                    <option value="1">Support</option>
                                    <option value="2">Billing</option>
                                    <option value="3">Management</option>
                                    <option value="4">Feature Request</option>
                                </select>
                            </div> --}}
                            <div class="mb-4">
                                <label class="form-label" for="remarks">Message</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="4"
                                    placeholder="Enter Remarks"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="block-content block-content-full block-content-sm text-end border-top">
                        <button type="button" id="editTransactionModalBtn" class="btn btn-alt-success">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    @include('modals.transaction_list_modal')

@endsection




@section('js')


    <script src="{{ asset('js/plugins/datatables-select/js/dataTables.select.js') }}"></script>
    <script src="{{ asset('js/plugins/datatables-select/js/select.dataTables.js') }}"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" type="module"></script>

    <script src="{{ asset('js/plugins/masked-inputs/jquery.maskedinput.min.js') }}"></script>

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
                            url: '{{ route('fetch_transaction_modal') }}',
                            data: {
                                transacNum,
                                status,
                                path,
                                _token: '{{ csrf_token() }}'
                            }

                        },
                        columns: [{
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
                            {
                                data: 'action'
                            },
                        ],
                        scrollX: true,
                        drawCallback: function() {
                            $(".receivedBtn").tooltip();

                            $('#editTransactionBtn').click(function (){
                                const editModal = new bootstrap.Modal(document.getElementById('transactionListModalEdit'));
                                editModal.show();

                                jQuery(function($){
                                $("#mobileNumber").mask("0999 999 9999",{placeholder:" "});
                                });

                                const id = $(this).data('id');
                                const branch = $(this).data('branch');
                                const mn = $(this).data('mn');
                                const cn = $(this).data('cn');
                                const amount = $(this).data('amount');
                                const remarks = $(this).data('remarks');


                                $("#hiddenTransactionId").val(id)
                                $("#clientName").val(cn)
                                $("#mobileNumber").val(mn)
                                $("#amount").val(amount)
                                $("#remarks").val(remarks)



                                var editModalBackdrop = document.querySelector('.modal-backdrop');
                                if (editModalBackdrop) {
                                    editModalBackdrop.style.zIndex = 1040;
                                    // editModalBackdrop.style.opacity = 0;
                                }
                            })


                            $("#deleteTransactionsBtn").click(function (){
                                const id = $(this).data('id');

                                const confirm = Swal.mixin({
                                    customClass: {
                                        confirmButton: "btn btn-success ms-2",
                                        cancelButton: "btn btn-danger"
                                    },
                                    buttonsStyling: false
                                });

                                confirm.fire({
                                    title: "Delete?",
                                    text: "Are you sure you want to delete this transaction?",
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonText: "Yes!",
                                    cancelButtonText: "Back",
                                    reverseButtons: true
                                }).then((result) => {
                                    if (result.isConfirmed) {

                                        $.ajax({
                                            url: '{{ route('delete_transaction') }}',
                                            method: 'post',
                                            data: {
                                                id,
                                                _token: '{{ csrf_token() }}',
                                            },
                                            success(result) {
                                                $("#modalTable").DataTable().ajax.reload();
                                                showToast("success","Transaction Deleted");
                                            }
                                        })

                                    } else if (
                                        /* Read more about handling dismissals below */
                                        result.dismiss === Swal.DismissReason.cancel
                                    ) {

                                    }
                                });

                            
                            })
                        }
                    });
                    $("#editTransactionModalBtn").click(function(){
                        const id = $("#hiddenTransactionId").val()
                        const cn = $("#clientName").val()
                        const mn = $("#mobileNumber").val()
                        const amount = $("#amount").val()
                        const remarks = $("#remarks").val()

                        if(!id || !cn || !mn || !amount || !remarks){
                            showToast("error","Please fill-up all fields!");
                            return
                        }

                        $.ajax({
                            url: '{{route('edit_transactions')}}',
                            method: 'POST',
                            data: {id, cn, mn, amount, remarks, 
                                _token: '{{csrf_token()}}'},
                            success(){
                                $("#modalTable").DataTable().ajax.reload();
                                $("#transactionListModalEdit").modal('hide');
                            } 
                        })
                    })
                }, 200);
            })
        })
    </script>
    <script src="{{ asset('js\lib\fileupload.js') }}"></script>
@endsection
