<div id="vendor-flowchart-header-model" class="modal fade" role="dialog" style="z-index: 999999;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vendor Flow charts</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="database-form">
                            @csrf
                            <div class="row">
                                <div class="col-12 pb-3">
                                    @php
                                    $vendorLists = App\Vendor::whereNotNull('flowchart_date')->orderBy('name','asc')->get();
                                    @endphp
                                    <select class="form-control col-md-6 mr-3" name="fc_vendor_id" id="fc_vendor_id">
                                        <option value="">Select Vendor</option>
                                            @foreach ($vendorLists as $vendord)
                                                <option value="{{ $vendord->id }}">{{ $vendord->name }}</option>
                                            @endforeach
                                    </select>
                                    <button type="button" class="btn btn-secondary btn-vendor-search-flowchart" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12 show-vendor-search-flowchart-list" id=""></div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="vfc-remarks-histories-list-header-fc" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Remarks Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Remarks</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vfc-remarks-histories-list-view-header-fc">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="fl-status-histories-list-header-fc" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="fl-status-histories-list-view-header-fc">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vendor-qa-header-model" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vendor Question Answers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="database-form">
                            <?php echo csrf_field(); ?>
                            <div class="row">
                                <div class="col-12 pb-3">
                                    @php
                                    $vendorLists = App\Vendor::where('question_status', 1)->orderBy('name','asc')->get();
                                    @endphp
                                    <select class="form-control col-md-6 mr-3" name="qa_vendor_id" id="qa_vendor_id">
                                        <option value="">Select Vendor</option>
                                            @foreach ($vendorLists as $vendord)
                                                <option value="{{ $vendord->id }}">{{ $vendord->name }}</option>
                                            @endforeach
                                    </select>
                                    <button type="button" class="btn btn-secondary btn-vendor-search-qa" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12 show-vendor-search-qa-list" id="">

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="qa-status-histories-list-header-qa" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="qa-status-histories-list-view-header-qa">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vqa-answer-histories-list-header-qa" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Answer Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Answer</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vqa-answer-histories-list-view-header-qa">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vendor-rqa-header-model" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vendor Rating Question Answers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <form id="database-form">
                            @csrf
                            <div class="row">
                                <div class="col-12 pb-3">
                                    @php
                                    $vendorLists = App\Vendor::where('rating_question_status', 1)->orderBy('name','asc')->get();
                                    @endphp
                                    <select class="form-control col-md-6 mr-3" name="rqa_vendor_id" id="rqa_vendor_id">
                                        <option value="">Select Vendor</option>
                                            @foreach ($vendorLists as $vendord)
                                                <option value="{{ $vendord->id }}">{{ $vendord->name }}</option>
                                            @endforeach
                                    </select>
                                    <button type="button" class="btn btn-secondary btn-vendor-search-rqa" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12 show-vendor-search-rqa-list" id="">

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="rqa-status-histories-list-header-rqa" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Status</th>
                                <th width="30%">New Status</th>
                                <th width="20%">Updated BY</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="rqa-status-histories-list-view-header-rqa">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="vqar-answer-histories-list-header-rqa" class="modal fade" role="dialog"  style="z-index: 999999;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Rating Answer Histories</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Rating</th>
                                <th width="30%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="vqar-answer-histories-list-view-header-rqa">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
