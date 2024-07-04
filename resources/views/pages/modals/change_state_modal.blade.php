<div class="modal fade" id="changeTransferStateModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="modal-popin"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">TOOLS AND EQUIPMENTS</h3>
                    <div class="block-options">
                        <button type="button" class="btn-block-option closeModalRfteis" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <table id="modalTable" class="table fs-sm table-bordered table-hover table-vcenter w-100">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Asset Code</th>
                                <th>Serial#</th>
                                <th>Item Code</th>
                                <th>Item Desc</th>
                                <th>Brand</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyModal">

                        </tbody>
                    </table>
                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button id="addStateBtn" type="button" class="btn btn-alt-primary closeModalRfteis">
                        Change  
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
