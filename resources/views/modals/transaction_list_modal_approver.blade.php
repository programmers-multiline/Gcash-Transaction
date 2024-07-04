<div class="modal fade" id="transactionListModal" tabindex="-1" role="dialog" aria-labelledby="modal-popin"
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
                <div class="block-content fs-sm">
                    <div class="d-flex gap-2 justify-content-end">
                        <button type="button" id="approveBtn" class="btn btn-success mb-3"><i
                                class="fa fa-check me-1"></i>Approved</button>
                        <button type="button" id="declineBtn" class="btn btn-danger mb-3"><i
                                class="fa fa-xmark me-1"></i>Decline</button>
                    </div>
                    <table id="modalTable"
                        class="table fs-sm table-bordered table-hover table-vcenter w-100 table-vcenter js-dataTable-responsive">
                        <thead>
                            <tr>
                                <th class="bg-warning text-light text-center" colspan="4">Collections</th>
                                <th class="bg-earth text-light text-center" colspan="3">Accounting/Treasury</th>
                                <th class="bg-secondary text-light text-center">Other</th>
                            </tr>
                            <tr>
                                <th id="selectedToolsContainer" style="padding-right: 10px;"></th>
                                <th>Id</th>
                                <th style="text-align: center;">Mobile Number</th>
                                <th style="text-align: center;">Client Name</th>
                                <th style="text-align: center;">Mobile Number</th>
                                <th style="text-align: center;">Client Name</th>
                                <th style="text-align: center;">Status</th>
                                <th style="text-align: center;">Approver Status</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
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
