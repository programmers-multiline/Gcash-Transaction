<div class="modal fade" id="transactionListModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal-popin"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-fromleft" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <input type="hidden" id="path" value="{{ request()->path() }}">
                    <h3 class="block-title">TRANSACTION LIST</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class=" fs-sm">


                    <div class="block block-rounded overflow-hidden" style="border-top: 1px solid #d6d6d6; margin-bottom: 0;">
                        <ul class="nav nav-tabs nav-tabs-block" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="btabs-animated-fade-home-tab" data-bs-toggle="tab"
                                    data-bs-target="#btabs-animated-fade-home" role="tab"
                                    aria-controls="btabs-animated-fade-home" aria-selected="false" tabindex="-1">Manual
                                    Approve</button>
                            </li>
                            <li class="nav-item" data-bs-toggle="tooltip" aria-label="Not available right now" data-bs-original-title="Not available right now" role="presentation">
                                <button class="nav-link" id="btabs-animated-fade-profile-tab" data-bs-toggle="tab"
                                    data-bs-target="#btabs-animated-fade-profile" role="tab"
                                    aria-controls="btabs-animated-fade-profile" disabled aria-selected="true">Auto
                                    Approve</button>
                            </li>
                        </ul>
                        <div class="block-content tab-content overflow-hidden my-2">
                            <div class="tab-pane fade show active" id="btabs-animated-fade-home" role="tabpanel"
                                aria-labelledby="btabs-animated-fade-home-tab" tabindex="0">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="fw-normal">Manual Approve</h4>
                                    <div class="d-flex gap-2 justify-content-end">
                                        {{-- <button type="button" class="btn btn-success mb-3 d-block ms-auto"
                                            data-bs-toggle="modal" data-bs-target="#uploadTransactionAcc"><i
                                                class="fa fa-upload me-1"></i>Upload Transaction</button> --}}
                                        <button type="button" id="approveBtn" class="btn btn-success mb-3"><i
                                                class="fa fa-check me-1"></i>Approved</button>
                                        <button type="button" id="declineBtn" class="btn btn-danger mb-3"><i
                                                class="fa fa-xmark me-1"></i>Decline</button>
                                    </div>
                                </div>
                                <table id="modalTable"
                                    class="table fs-sm table-bordered table-hover table-vcenter w-100">
                                    <thead>
                                        <tr>
                                            <th id="selectedToolsContainer" style="padding-right: 10px;"></th>
                                            <th>Id</th>
                                            <th>Branch</th>
                                            <th class="click">Mobile Number</th>
                                            <th>Client Name</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th>status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                            <div class="tab-pane fade" id="btabs-animated-fade-profile" role="tabpanel"
                                aria-labelledby="btabs-animated-fade-profile-tab" tabindex="0">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    {{-- <h4 class="fw-normal mb-0">Auto Approve</h4> --}}
                                    <form id="uploadTransactionForm" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="d-flex gap-2 align-items-center">
                                            <div>
                                                <input type="hidden" id="routeUrl"
                                                    value="{{ route('upload_transaction_acc') }}">
                                                <label class="form-label" for="transactionUpload">Upload
                                                    Transactions Here.</label>
                                                <input class="form-control" type="file" name='importTransaction'
                                                    accept=".csv, .xlsx, .xls" id="transactionUpload">
                                            </div>
                                            <input type="hidden" id="transactionNumber" name="transactionNumber" value="">
                                            <input type="submit" class="btn btn-alt-success mt-4" value="Upload">
                                        </div>
                                    </form>
                                    <div class="d-flex gap-2 justify-content-end align-items-center">
                                        <button type="button" id="approveBtnAuto" class="btn btn-success mt-4"><i
                                                class="fa fa-check me-1"></i>Approved</button>
                                        {{-- <button type="button" id="declineBtn" class="btn btn-danger mt-4"><i
                                                class="fa fa-xmark me-1"></i>Decline</button> --}}
                                    </div>
                                </div>
                                <table id="modalTableAuto"
                                    class="table fs-sm table-bordered table-hover table-vcenter w-100">
                                    <thead>
                                        <tr>
                                            <th class="bg-warning text-light text-center" colspan="4">Collections</th>
                                            <th class="bg-earth text-light text-center" colspan="4">Accounting</th>
                                            <th class="bg-secondary text-light text-center">Other</th>
                                        </tr>
                                        <tr>
                                            <th>Id</th>
                                            <th>Mobile Number</th>
                                            <th>Client Name</th>
                                            <th>Amount</th>
                                            <th>Mobile Number</th>
                                            <th>Client Name</th>
                                            <th>Amount</th>
                                            <th>Upload Status</th>
                                            <th>status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    {{-- modal upload transaction --}}
                    {{-- 
                    <div class="modal fade" id="uploadTransactionAcc" tabindex="-1" role="dialog"
                        aria-labelledby="modal-popin" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-fromleft" role="document">
                            <div class="modal-content">
                                <form id="uploadTransactionForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="routeUrl"
                                        value="{{ route('upload_transaction_acc') }}">
                                    <div class="block block-rounded shadow-none mb-0">
                                        <div class="block-header block-header-default">
                                            <h3 class="block-title">Upload Transaction</h3>
                                            <div class="block-options">
                                                <button type="button" class="btn-block-option"
                                                    data-bs-dismiss="modal" aria-label="Close">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="block-content fs-sm">
                                            <div class="block block-rounded">
                                                <div class="block-content">
                                                    <div class="mb-4">
                                                        <label class="form-label" for="transactionUpload">Upload
                                                            Transactions Here.</label>
                                                        <input class="form-control" type="file"
                                                            name='importTransaction' accept=".csv, .xlsx, .xls"
                                                            id="transactionUpload">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="block-content block-content-full block-content-sm text-end border-top">
                                            <button type="button" id="closeModal" class="btn btn-alt-secondary"
                                                data-bs-dismiss="modal">
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
                    </div> --}}

                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="closeModal" class="btn btn-alt-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
