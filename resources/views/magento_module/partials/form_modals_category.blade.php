<div id="moduleCategoryCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            {{-- {!! Form::open(['route' => 'magento_module_categories.store', 'method' => 'POST', 'id'=>'module_category_form', 'class' => 'form mb-15', 'enctype' => 'multipart/form-data']) !!} --}}
            <form id="module_category_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add Module Category</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body">
                <div class="form-group">
                    <strong>Module Category :</strong>
                    {!! Form::text('category_name', null, ['placeholder' => 'Module Category', 'id' => 'category_name', 'class' => 'form-control', 'required' => 'required']) !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-secondary">Add</button>
            </div>
            </form>
            {{-- {!! Form::close() !!} --}}
        </div>
    </div>
</div>

<div id="moduleCategoryEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">Edit Module Category</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="modal-body">
                        <div class="form-group">
                            <strong>Module Category :</strong>
                            {!! Form::text('category_name', null, ['placeholder' => 'Module Type', 'id' => 'category_name', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('category_name'))
                                <div class="alert alert-danger">{{ $errors->first('category_name') }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
    <script>

    $(document).on('submit', '#module_category_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("module_category_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
        $.ajax({
            url: '{{ route("magento_module_categories.store") }}',
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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
            success: function(response) {
                $('#moduleCategoryCreateModal #module_category_form').trigger('reset');
                $('#moduleCategoryCreateModal #module_category_form').find('.error-help-block').remove();
                $('#moduleCategoryCreateModal #module_category_form').find('.invalid-feedback').remove();
                $('#moduleCategoryCreateModal #module_category_form').find('.alert').remove();
                toastr["success"](response.message);
                location.reload();
            },
            error: function(xhr, status, error) { // if error occured
                if(xhr.status == 422){
                    var errors = JSON.parse(xhr.responseText).errors;
                    customFnErrors(self, errors);
                }
                else{
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                }
            },
        });
    });
    </script>

@endpush