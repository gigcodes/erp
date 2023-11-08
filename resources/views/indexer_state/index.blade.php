@extends('layouts.app')

@section('content')
<style>
    .index-invalidate {
        background-color: red;
        color: white;
        letter-spacing: 2px;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
    }

    .index-valid {
        background-color: #00ff00;
        color: white;
        letter-spacing: 2px;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
    }

    .index-running {
        background-color: gray;
        color: white;
        letter-spacing: 2px;
    font-weight: bold;
    font-size: 15px;
    text-align: center;
    }
</style>
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Indexer State <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 pl-5 pr-5">
            <div id="elasticConnection">

            </div>
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3" id="reindex-table">
                        @include('indexer_state.list')
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="role-create-new" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><b>Create new User</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <form action="" method="post">
                                <?php echo csrf_field(); ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="table-responsive mt-3">
                                            <input hidden type="text" class="form-control" name="id"
                                                   placeholder="Enter id" id="role-id">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" class="form-control" name="name"
                                                       placeholder="Enter name" id="role-name">
                                            </div>
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select id="role-type" class="form-control input-sm"
                                                        name="type" required>
                                                    <option value="1">User (default)</option>
                                                    <option value="2">Admin</option>
                                                    <option value="3">Super admin</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Readonly</label>
                                                <input type="checkbox" class="form-check-input" name="readonly"
                                                       id="role-readonly">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <button type="submit"
                                                    class="btn btn-secondary submit_create_role float-right float-lg-right">
                                                Submit
                                            </button>
                                        </div>
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
        $(document).on("click", ".create-new-role", function (e) {
            e.preventDefault();
            $('#role-create-new').modal('show');
            restoreForm();
        });

        $(document).on("click", ".submit_create_role", function (e) {
            e.preventDefault();
            var url = "";
            var formData = $(this).closest('form').serialize();

            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                success: function (resp) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#store-create-project').modal('hide');
                    if (resp.code == 200) {
                        toastr["success"](resp.message);
                    } else {
                        toastr["error"](resp.message);
                    }
                },
                error: function (err) {
                    $('#loading-image-preview').hide();
                    $('#website-project-name').val("");
                    $('#role-create-new').modal('hide');
                    toastr["error"](err.responseJSON.message);
                }
            })
        });

        $('a.btn-edit-role').click(function(e) {
            e.preventDefault();
            $('#role-create-new').modal('show');

            restoreForm();

            $('#role-id').val($(this).attr('data-id'));

            let data = JSON.parse($(this).attr('data-json'));

            $('#role-name').val(data.name);
            $('#role-id').val(data.roleid);
            $("#role-type option[value='" + data.type + "']").prop("selected", true);

            if (data.readonly !== "0") {
                console.log('Checked', data.readonly);
                $('#role-readonly').attr('checked', data.readonly);
            }
        });

        var restoreForm = function() {
            $('#role-id').val('');
            $('#role-name').val('');
            $('#role-type').val('');
            $('#role-readonly').removeAttr('checked');
        }

        $(document).ready(function() {
            var url = "{{ route('indexer-state.elastic-conn') }}";

            let elastic = $("#elasticConnection");
            $.ajax({
                url: url,
                method: 'GET',
                success: function (resp) {
                    if (resp.code == 200) {
                        elastic.html('<div class="alert alert-success" role="alert">'+resp.message+'</div>');
                    } else {
                        elastic.html('<div class="alert alert-danger" role="alert">'+resp.message+'</div>');
                    }
                },
                error: function (err) {
                    toastr["error"](err.responseJSON.message);
                    elastic.html('<div class="alert alert-danger" role="alert">'+err.responseJSON.message+'</div>');
                }
            })
        });

        $(document).on('click', 'a.btn-reindex-indexer', function (e) {
            e.preventDefault();

            let indexerStateId = $(this).attr('data-id');

            var url = "{{ route('indexer-state.reindex') }}?id=" + indexerStateId;

            let reindexTable = $("#reindex-table");
            $('#loading-image-preview').show();
            $.ajax({
                url: url,
                method: 'GET',
                success: function (resp) {
                    toastr["success"](resp.message);
                },
                error: function (err) {
                    toastr["error"](err.responseJSON.message);
                }
            })
            setTimeout(function() {
                $.ajax({
                    url: "{{ route('indexer-state.index') }}",
                    method: 'GET',
                    success: function (resp) {
                        let reindexTable = $("#reindex-table");
                        reindexTable.empty();
                        reindexTable.html(resp.tpl);
                    }
                })
            }, 2000)
            $('#loading-image-preview').hide();
        })
    </script>
@endsection