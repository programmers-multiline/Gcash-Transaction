<div class="modal fade" id="transactionListModal" tabindex="-1" role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
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
                    <table id="modalTable"
                    class="table fs-sm table-bordered table-hover table-vcenter w-100">
                    <thead>
                        <tr>
                            <th>Mobile Number</th>
                            <th>Client Name</th>
                            <th>Pension Type</th>
                            <th>Pension Number</th>
                            <th>Amount</th>
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