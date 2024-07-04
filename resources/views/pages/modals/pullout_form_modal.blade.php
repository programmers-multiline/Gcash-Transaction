<div class="modal fade" id="pulloutRequestModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    role="dialog" aria-labelledby="modal-popin" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-popin" role="document">
        <div class="modal-content">
            <div class="block block-rounded shadow-none mb-0">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Pull-Out Form</h3>
                    <div class="block-options closeModalRfteis">
                        <button type="button" class="btn-block-option" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content fs-sm">
                    <form id="pulloutFrom">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label" for="client">Client <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="client" name="client"
                                    placeholder="Enter Client">
                            </div>
                            <div class="col-5">
                                {{-- <label class="form-label" for="projectName">Project Name <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="projectName" name="projectName" size="1">
                                    <option disabled selected>Select Project Name</option>
                                    @foreach ($pg as $project_detail)
                                        <option data-pcode="{{ Str::title($project_detail->project_code) }}"
                                            data-custname="{{Str::title($project_detail->customer_name)}}"
                                            data-paddress="{{ Str::title($project_detail->project_address) }}"
                                            value="{{ $project_detail->project_name }}">
                                            {{ $project_detail->project_name }}
                                        </option>
                                    @endforeach
                                </select> --}}
                                <label class="form-label" for="projectName">Project Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="projectName" name="projectName"
                                placeholder="Enter Project Name">
                            </div>
                            <div class="col-2">
                                <label class="form-label" for="contact">Contact No. <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="contact" name="contact"
                                    placeholder="Enter Contact No.">
                            </div>
                            <div class="col-2">
                                <label class="form-label" for="dateToPick">Date to Pick-up <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="dateToPick" name="dateToPick" min="{{ date('Y-m-d') }}"
                                    placeholder="Enter Date">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-3">
                                <label class="form-label" for="projectCode">Project Code <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectCode" name="projectCode"
                                    placeholder="Enter Project code" >
                            </div>
                            <div class="col-3">
                                <label class="form-label" for="subcon">SubContractor</label>
                                <input type="text" class="form-control" id="subcon" name="subcon"
                                    placeholder="Enter Subcon">
                            </div>
                            <div class="col-6">
                                <label class="form-label" for="projectAddress">Project Address <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="projectAddress" name="projectAddress"
                                    placeholder="Enter Project Address" >
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="reason">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Enter Your Reason.."></textarea>
                        </div>
                    </form>
                    <hr class="mt-5">
                    <h3 class="mb-1 text-secondary">Selected Tools</h3>
                    <table class="table table-hover table-bordered table-vcenter">
                        <thead>
                            <tr>
                                <th>TEIS</th>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>PM/TL/PE Recommendation</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyPulloutModal">
                        </tbody>
                    </table>
                </div>
                <div class="block-content block-content-full block-content-sm text-end border-top">
                    <button type="button" id="closeModal" class="btn btn-alt-secondary closeModalRfteis"
                        data-bs-dismiss="modal">
                        Close
                    </button>
                    <button id="requestPulloutModalBtn" type="button" class="btn btn-alt-primary">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
