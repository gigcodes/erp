<div id="moduleCreateModal" class="modal fade " role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            {{-- {!! Form::open(['route' => 'magento_module_types.store', 'method' => 'POST', 'class' => 'form mb-15', 'enctype' => 'multipart/form-data']) !!} --}}
            <form id="magento_module_form" class="form mb-15" >
            @csrf
            <div class="modal-header">
                <h4 class="modal-title">Add {{ $title }}</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @include('magento_module.partials.form')

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

<div id="moduleEditModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <form id="magento_module_edit_form" method="POST">
                @csrf
                @method('PUT')
                {!! Form::hidden('id', null, ['id'=>'id']) !!}
                <div class="modal-header">
                    <h4 class="modal-title">Update Store Color</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    @include('magento_module.partials.form')
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

    $(document).on('submit', '#magento_module_form', function(e){
        e.preventDefault();

        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_form"));
        var button = $(this).find('[type="submit"]');
        console.log("Submit magento_module_form");
        $.ajax({
            url: '{{ route("magento_modules.store") }}',
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
                $('#moduleCreateModal #magento_module_form').trigger('reset');
                $('#moduleCreateModal #magento_module_form').find('.error-help-block').remove();
                $('#moduleCreateModal #magento_module_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #magento_module_form').find('.alert').remove();
                $('#magento_module_form').modal('hide');
                oTable.draw();
                toastr["success"](response.message);
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

    $(document).on('click', '.edit-magento-module', function() {
          var magento_module = $(this).data('row');
          console.log((magento_module));
          $('#magento_module_edit_form #id').val(magento_module.id);
          $('#magento_module_edit_form #module_category_id').val(magento_module.module_category_id);
          $('#magento_module_edit_form #module').val(magento_module.module);
          $('#magento_module_edit_form #current_version').val(magento_module.current_version);
          $('#magento_module_edit_form #module_type').val(magento_module.module_type);
          $('#magento_module_edit_form #payment_status').val(magento_module.payment_status);
          $('#magento_module_edit_form #status').val(magento_module.status);
          $('#magento_module_edit_form #task_status').val(magento_module.task_status);
        //   $('#magento_module_edit_form #cron_time').val(magento_module.cron_time);
          $('#magento_module_edit_form #is_js_css').val(magento_module.is_js_css);
          $('#magento_module_edit_form #is_third_party_js').val(magento_module.is_third_party_js);
          $('#magento_module_edit_form #is_sql').val(magento_module.is_sql);
          $('#magento_module_edit_form #is_third_party_plugin').val(magento_module.is_third_party_plugin);
          $('#magento_module_edit_form #developer_name').val( parseInt(magento_module.developer_id) );
          $('#magento_module_edit_form #is_customized').val(magento_module.is_customized);
          $('#magento_module_edit_form #module_description').val(magento_module.module_description);
          $('#magento_module_edit_form #api').val(magento_module.api);
          $('#magento_module_edit_form #cron_job').val(magento_module.cron_job);
          $('#magento_module_edit_form #site_impact').val(magento_module.site_impact);
          $('#magento_module_edit_form #composer').val(magento_module.composer);
          $('#magento_module_edit_form #dependency').val(magento_module.dependency);
          $('#moduleEditModal').modal('show');
    });

    $(document).on('submit', '#magento_module_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_edit_form"));
        var magento_module_id = $('#magento_module_edit_form #id').val();
        console.log(formData, magento_module_id);
        var button = $(this).find('[type="submit"]');
        console.log("#magento_module_edit_form submit");
        $.ajax({
            url: '{{ route("magento_modules.update", '') }}/' + magento_module_id,
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
                button.html('Update');
                button.prop('disabled', false);
                button.removeClass('disabled');
            },
            success: function(response) {
                $('#moduleCreateModal #magento_module_edit_form').find('.error-help-block').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.invalid-feedback').remove();
                $('#moduleCreateModal #magento_module_edit_form').find('.alert').remove();
                oTable.draw();
                toastr["success"](response.message);
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