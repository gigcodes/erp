@extends('layouts.app')

@section('content')
    <div class="row" id="common-page-layout">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Zabbix Roles <span class="count-text"></span></h2>
        </div>
        <br>
        <div class="col-lg-12 margin-tb" id="page-view-result">
            <div class="col-lg-12 pl-5 pr-5">
                <div style="display: flex !important; float: right !important;">
                    <div>
                        <a href="#" class="btn btn-xs btn-secondary create-new-role">Create</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-12 pl-5 pr-5">
            <form action="/store-website/generate-api-token" method="post">
                <?php echo csrf_field(); ?>

                <div class="col-md-12">
                    <div class="table-responsive mt-3">
                        @include('zabbix.role.list')
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
                                                <label>Surname</label>
                                                <input type="text" class="form-control" name="surname"
                                                       placeholder="Enter surname" id="role-surname">
                                            </div>
                                            <div class="form-group">
                                                <label>Username</label>
                                                <input type="text" class="form-control" name="rolename"
                                                       placeholder="Enter rolename" id="role-rolename">
                                            </div>
                                            <div class="form-group">
                                                <label>Role ID</label>
                                                <input type="text" class="form-control" name="role_id"
                                                       placeholder="Enter Role ID" id="role-role-id">
                                            </div>
                                            <div class="form-group">
                                                <label>Url</label>
                                                <input type="text" class="form-control" name="url"
                                                       placeholder="Enter url" id="role-url">
                                            </div>
                                            <div class="form-group">
                                                <label>Password</label>
                                                <input type="text" class="form-control" name="password"
                                                       placeholder="Enter password" id="role-password">
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
            var url = "{{ route('zabbix.role.save') }}";
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
                        let role = resp.role;
                        let roleId = role.id;
                        console.log('.td-description-' + roleId);
                        $('.td-description-' + roleId).text(role.description);
                        $('.td-type-' + roleId).text(role.type);
                        $('.td-location-' + roleId).text(role.location);
                        $('.td-created-at-' + roleId).text(role.created_at);
                        $('.td-store-websites-' + roleId).text(role.store_website_id);
                        $('.td-edit-' + roleId).attr('data-json', resp.role_json);
                        if (!role.is_active) {
                            $('.td-is-active-' + roleId).removeAttr('checked');
                        } else {
                            $('.td-is-active-' + roleId).attr('checked', role.is_active);
                        }


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
            $('#role-surname').val(data.surname);
            $('#role-rolename').val(data.rolename);
            $('#role-url').val(data.url);
            $('#role-role-id').val(data.role_id);
            $('#role-id').val(data.id);
        });

        var restoreForm = function() {
            $('#role-id').val('');
            $('#role-name').val('');
            $('#role-surname').val('');
            $('#role-rolename').val('');
            $('#role-role-id').val('');
            $('#role-url').val('');
            $('#role-password').val('');
        }
    </script>
@endsection