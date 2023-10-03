<div id="SearchGoogleDocModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Search Google Docs</h5>
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
                                    <input type="text" name="task_search" class="google-doc-search-table" class="form-control" placeholder="Enter File Name">
                                    <button type="button" class="btn btn-secondary btn-google-doc-search-menu" ><i class="fa fa-search"></i></button>
                                </div>
                                <div class="col-12">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                        <tr>
                                            <th width="2%">ID</th>
                                            <th width="4%">No</th>
                                            <th width="10%">File Name</th>
                                            <th width="10%">Category</th>
                                            <th width="10%">Task</th>
                                            <th width="10%">Created By</th>
                                            <th width="10%">Created Date</th>
                                            <th width="10%">URL</th>
                                            <th width="10%">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody class="show-search-google-doc-list">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(".btn-google-doc-search-menu").on("click", function(){
        var keyword = $('.google-doc-search-table').val();

        $.ajax({
            url: '{{route('google-docs.google.module.search')}}',
            type: 'GET',
            data: {
                subject: keyword,
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            // dataType: 'json',
            beforeSend: function () {
                $("#loading-image").show();
            },
            success: function (response) {
                console.log("Responsessssssssssss");
                console.log(response);
                $("#loading-image").hide();
                $('.show-search-google-doc-list').html(response);
            },
            error: function () {
                $("#loading-image").hide();
                toastr["Error"]("An error occured!");
            }
        });
    });
</script>