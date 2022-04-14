<div id="moduleTypeCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            {{-- {!! Form::open(['route' => 'magento_module_types.store', 'method' => 'POST', 'class' => 'form mb-15', 'enctype' => 'multipart/form-data']) !!} --}}
            <form id="module_type_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add Module Type</h4>
                <button type="button" class="close" data-dismiss="modal">&times; </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <strong>Module Type :</strong>
                    {!! Form::text('magento_module_type', null, ['placeholder' => 'Module Type', 'id' => 'magento_module_type', 'class' => 'form-control', 'required' => 'required']) !!}
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

<div id="moduleTypeEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <form action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">Edit Module Type</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <strong>Module Type :</strong>
                        {!! Form::text('magento_module_type', null, ['placeholder' => 'Module Type', 'id' => 'magento_module_type', 'class' => 'form-control', 'required' => 'required']) !!}
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

$(document).on('submit', '#module_type_form', function(e){
    e.preventDefault();
    var self = $(this);
    let formData = new FormData(document.getElementById("module_type_form"));
    var button = $(this).find('[type="submit"]');
    console.log(button);
    $.ajax({
        url: '{{ route("magento_module_types.store") }}',
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
            $('#moduleTypeCreateModal #module_type_form').trigger('reset');
            $('#moduleTypeCreateModal #module_type_form').find('.error-help-block').remove();
            $('#moduleTypeCreateModal #module_type_form').find('.invalid-feedback').remove();
            $('#moduleTypeCreateModal #module_type_form').find('.alert').remove();
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