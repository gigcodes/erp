<div class="modal fade" id="create-magento-frontend-docs" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Create Magento Frotend Documentation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" id="magento-frontend-create" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>category</label>
                            <select class="form-control select-multiple" id="category-select"
                                name="magento_docs_category_id" required>
                                @php
                                    $storecategories = \App\SiteDevelopmentCategory::select('title', 'id')
                                        ->wherenotNull('title')
                                        ->get();
                                @endphp

                                <option value="">Select Category</option>
                                @foreach ($storecategories as $storecategory)
                                    <option value="{{ $storecategory->id }}">{{ $storecategory->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>location</label>
                            {!! Form::text('location', null, [
                                'id' => 'location',
                                'placeholder' => 'Magento Frontend location',
                                'class' => 'form-control location',
                                'required' => 'required',
                            ]) !!}

                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Admin Configuration</label>
                            {!! Form::text('admin_configuration', null, [
                                'id' => 'admin_configuration',
                                'placeholder' => 'Magento Admin Configuration',
                                'class' => 'form-control admin_configuration',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Frontend configuration</label>
                            {!! Form::text('frontend_configuration', null, [
                                'id' => 'frontend_configuration',
                                'placeholder' => 'Magento Frontend configuration',
                                'class' => 'form-control frontend_configuration',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Upload File</label>
                            <input type="file" name="file[]" id="fileInput" class="form-control input-sm"
                                placeholder="Upload File">
                        </div>
                    </div>

                    @if (auth()->user() &&
                            auth()->user()->isAdmin())
                        @php
                            $users = \App\User::select('id', 'name', 'email', 'gmail')
                                ->whereNotNull('gmail')
                                ->get();
                        @endphp
                        <div class="col-sm-12">
                            <div class="form-group custom-select2">
                                <label>Read Permission for Users
                                </label>
                                <select class="w-100 js-example-basic-multiple js-states" id="id_label_permission_read"
                                    multiple="multiple" name="read[]" required>
                                    @foreach ($users as $val)
                                        <option value="{{ $val->gmail }}" class="form-control">{{ $val->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group custom-select2">
                                <label>Write Permission for Users
                                </label>
                                <select class="w-100 js-example-basic-multiple js-states" id="id_label_permission_write"
                                    multiple="multiple" name="write[]" required>
                                    @foreach ($users as $val)
                                        <option value="{{ $val->gmail }}" class="form-control">{{ $val->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    $("#id_label_permission_read").select2();
    $("#id_label_permission_write").select2();

    $(document).on('submit', '#magento-frontend-create', function(e) {
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento-frontend-create"));
        var button = $(this).find('[type="submit"]');
        $.ajax({
            url: '{{ route('magento-frontend-store') }}',
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function() {
                button.html(spinner_html);
                button.prop('disabled', true);
                button.addClass('disabled');
            },
            complete: function() {
                button.html('Add');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(data) {
                // $('#moduleCreateModal #magento_module_form').trigger('reset');
                // $('#moduleCreateModal #magento_module_form').find('.error-help-block').remove();
                // $('#moduleCreateModal #magento_module_form').find('.invalid-feedback').remove();
                // $('#moduleCreateModal #magento_module_form').find('.alert').remove();
                $('#magento-frontend-create').modal('hide');
                // oTable.draw();
                toastr["success"](data.message);
            },
            error: function(xhr, status, error) { // if error occured
                if (xhr.status == 422) {
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                } else {
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });
</script>