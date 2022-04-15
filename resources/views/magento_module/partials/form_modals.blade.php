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
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Module Category :</strong>
                            {!! Form::select('module_category_id', $module_categories, null, ['id'=>'module_category_id', 'placeholder' => 'Select Module Category', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('module_category_id'))
                                <span style="color:red">{{ $errors->first('module_category_id') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Website :</strong>
                            {!! Form::select('store_website_id', $store_websites, null, ['id'=>'module_category_id', 'placeholder' => 'Select Module Category', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('module_category_id'))
                                <span style="color:red">{{ $errors->first('module_category_id') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Module Name:</strong>
                            {!! Form::text('module', null, ['id'=>'module', 'placeholder' => 'Module Name', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('module'))
                                <span style="color:red">{{ $errors->first('module') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Current Version:</strong>
                            {!! Form::text('current_version', null, ['id'=>'current_version', 'placeholder' => 'Current Version', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('current_version'))
                                <span style="color:red">{{ $errors->first('current_version') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Module Type:</strong>
                            {!! Form::select('module_type', $magento_module_types, null, ['id'=>'module_type', 'placeholder' => 'Select Module Type', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('module_type'))
                                <span style="color:red">{{ $errors->first('module_type') }}</span>
                            @endif
                        </div>
                    </div>
          
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Payment Status:</strong>
                            {!! Form::select('payment_status', ['Free' => 'Free', 'Paid' => 'Paid'], null, ['id'=>'payment_status', 'placeholder' => 'Select Payment Status', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('payment_status'))
                                <span style="color:red">{{ $errors->first('payment_status') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Status:</strong>
                            {!! Form::select('status', ['Disabled', 'Enable'], null, ['id'=>'status', 'placeholder' => 'Select Status', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('status'))
                                <span style="color:red">{{ $errors->first('status') }}</span>
                            @endif
                        </div>
                    </div>
            
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Cron Time (Min) :</strong>
                            {!! Form::text('cron_time', null, ['id'=>'cron_time', 'placeholder' => 'Cron Time', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('cron_time'))
                                <span style="color:red">{{ $errors->first('cron_time') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Javascript/css Require :</strong>
                            {!! Form::select('is_js_css', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_js_css', 'placeholder' => 'Select Javascript/css Require', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('is_js_css'))
                                <span style="color:red">{{ $errors->first('is_js_css') }}</span>
                            @endif
                        </div>
                    </div>
                
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Third Party JS Require :</strong>
                            {!! Form::select('is_third_party_js', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_third_party_js', 'placeholder' => 'Select Third Third Party JS Require ', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('is_third_party_js'))
                                <span style="color:red">{{ $errors->first('is_third_party_js') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Sql Query :</strong>
                            {!! Form::select('is_sql', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_sql', 'placeholder' => 'Select Sql Query Status', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('is_sql'))
                                <span style="color:red">{{ $errors->first('is_sql') }}</span>
                            @endif
                        </div>
                    </div>
                
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Third Party Plugin :</strong>
                            {!! Form::select('is_third_party_plugin', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_third_party_plugin', 'placeholder' => 'Select Third Party Plugin', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('is_third_party_plugin'))
                                <span style="color:red">{{ $errors->first('is_third_party_plugin') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="row ml-2 mr-2">
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Developer Name :</strong>
                            {!! Form::select('developer_name', $users, null, ['id'=>'developer_name', 'placeholder' => 'Select Sql Query Status', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('developer_name'))
                                <span style="color:red">{{ $errors->first('developer_name') }}</span>
                            @endif

                            {{-- <strong>Developer Name:</strong>
                            {!! Form::text('developer_name', null, ['id'=>'developer_name', 'placeholder' => 'Developer Name', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('developer_name'))
                                <span style="color:red">{{ $errors->first('developer_name') }}</span>
                            @endif --}}
                        </div>
                    </div>
                
                    <div class="col-xs-6 col-sm-6">
                        <div class="form-group">
                            <strong>Customized:</strong>
                            {!! Form::select('is_customized', ['No', 'Yes'], null, ['id'=>'is_customized', 'placeholder' => 'Customized', 'class' => 'form-control', 'required' => 'required']) !!}
                            @if ($errors->has('is_customized'))
                                <span style="color:red">{{ $errors->first('is_customized') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row ml-2 mr-2">
                    <div class="col-xs-12 col-sm-12">
                        <div class="form-group">
                            <strong>Module Description:</strong>
                            {!! Form::textarea('module_description', null, ['id'=>'module_description', 'placeholder' => 'Module Description', 'class' => 'form-control', 'required' => 'required', 'rows' => 2, 'cols' => 40]) !!}
                            @if ($errors->has('module_description'))
                                <span style="color:red">{{ $errors->first('module_description') }}</span>
                            @endif
                        </div>
                    </div>
                
                    {{-- <div class="col-xs-12 col-sm-10 ml-5 text-right">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div> --}}
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
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Module Category :</strong>
                                {!! Form::select('module_category_id', $module_categories, null, ['id'=>'module_category_id', 'placeholder' => 'Select Module Category', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('module_category_id'))
                                    <span style="color:red">{{ $errors->first('module_category_id') }}</span>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Website :</strong>
                                {!! Form::select('store_website_id', $store_websites, null, ['id'=>'module_category_id', 'placeholder' => 'Select Module Category', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('module_category_id'))
                                    <span style="color:red">{{ $errors->first('module_category_id') }}</span>
                                @endif
                            </div>
                        </div>
                    
                    </div>
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Module Name:</strong>
                                {!! Form::text('module', null, ['id'=>'module', 'placeholder' => 'Module Name', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('module'))
                                    <span style="color:red">{{ $errors->first('module') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Current Version:</strong>
                                {!! Form::text('current_version', null, ['id'=>'current_version', 'placeholder' => 'Current Version', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('current_version'))
                                    <span style="color:red">{{ $errors->first('current_version') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Module Type:</strong>
                                {!! Form::select('module_type', $magento_module_types, null, ['id'=>'module_type', 'placeholder' => 'Select Module Type', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('module_type'))
                                    <span style="color:red">{{ $errors->first('module_type') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Payment Status:</strong>
                                {!! Form::select('payment_status', ['Free' => 'Free', 'Paid' => 'Paid'], null, ['id'=>'payment_status', 'placeholder' => 'Select Payment Status', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('payment_status'))
                                    <span style="color:red">{{ $errors->first('payment_status') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Status:</strong>
                                {!! Form::select('status', ['Disabled', 'Enable'], null, ['id'=>'status', 'placeholder' => 'Select Status', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('status'))
                                    <span style="color:red">{{ $errors->first('status') }}</span>
                                @endif
                            </div>
                        </div>
                  
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Task Status:</strong>
                                {!! Form::select('task_status', $task_statuses, null, ['id'=>'task_status', 'placeholder' => 'Select Payment Status', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('task_status'))
                                    <span style="color:red">{{ $errors->first('task_status') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Cron Time (Min) :</strong>
                                {!! Form::text('cron_time', null, ['id'=>'cron_time', 'placeholder' => 'Cron Time', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('cron_time'))
                                    <span style="color:red">{{ $errors->first('cron_time') }}</span>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Javascript/css Require :</strong>
                                {!! Form::select('is_js_css', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_js_css', 'placeholder' => 'Select Javascript/css Require', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('is_js_css'))
                                    <span style="color:red">{{ $errors->first('is_js_css') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Third Party JS Require :</strong>
                                {!! Form::select('is_third_party_js', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_third_party_js', 'placeholder' => 'Select Third Third Party JS Require ', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('is_third_party_js'))
                                    <span style="color:red">{{ $errors->first('is_third_party_js') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Sql Query :</strong>
                                {!! Form::select('is_sql', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_sql', 'placeholder' => 'Select Sql Query Status', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('is_sql'))
                                    <span style="color:red">{{ $errors->first('is_sql') }}</span>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Third Party Plugin :</strong>
                                {!! Form::select('is_third_party_plugin', ['0' => 'No', '1' => 'Yes'], null, ['id'=>'is_third_party_plugin', 'placeholder' => 'Select Third Party Plugin', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('is_third_party_plugin'))
                                    <span style="color:red">{{ $errors->first('is_third_party_plugin') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Developer Name:</strong>
                                {!! Form::text('developer_name', null, ['id'=>'developer_name', 'placeholder' => 'Developer Name', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('developer_name'))
                                    <span style="color:red">{{ $errors->first('developer_name') }}</span>
                                @endif
                            </div>
                        </div>
                    
                        <div class="col-xs-6 col-sm-6">
                            <div class="form-group">
                                <strong>Customized:</strong>
                                {!! Form::select('is_customized', ['No', 'Yes'], null, ['id'=>'is_customized', 'placeholder' => 'Customized', 'class' => 'form-control', 'required' => 'required']) !!}
                                @if ($errors->has('is_customized'))
                                    <span style="color:red">{{ $errors->first('is_customized') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="row ml-2 mr-2">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                <strong>Module Description:</strong>
                                {!! Form::textarea('module_description', null, ['id'=>'module_description', 'placeholder' => 'Module Description', 'class' => 'form-control', 'required' => 'required', 'rows' => 2, 'cols' => 40]) !!}
                                @if ($errors->has('module_description'))
                                    <span style="color:red">{{ $errors->first('module_description') }}</span>
                                @endif
                            </div>
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

    $(document).on('submit', '#magento_module_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_form"));
        var button = $(this).find('[type="submit"]');
        console.log(button);
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
          console.log({magento_module})
          console.log(magento_module.category_name);
          $('#magento_module_edit_form #id').val(magento_module.id);
          $('#magento_module_edit_form #module_category_id').val(magento_module.module_category_id);
          $('#magento_module_edit_form #module').val(magento_module.module);
          $('#magento_module_edit_form #current_version').val(magento_module.current_version);
          $('#magento_module_edit_form #module_type').val(magento_module.module_type);
          $('#magento_module_edit_form #payment_status').val(magento_module.payment_status);
          $('#magento_module_edit_form #status').val(magento_module.status);
          $('#magento_module_edit_form #task_status').val(magento_module.task_status);
          $('#magento_module_edit_form #cron_time').val(magento_module.cron_time);
          $('#magento_module_edit_form #is_js_css').val(magento_module.is_js_css);
          $('#magento_module_edit_form #is_third_party_js').val(magento_module.is_third_party_js);
          $('#magento_module_edit_form #is_sql').val(magento_module.is_sql);
          $('#magento_module_edit_form #is_third_party_plugin').val(magento_module.is_third_party_plugin);
          $('#magento_module_edit_form #developer_name').val(magento_module.developer_name);
          $('#magento_module_edit_form #is_customized').val(magento_module.is_customized);
          $('#magento_module_edit_form #module_description').val(magento_module.module_description);

          $('#moduleEditModal').modal('show');
    });

    $(document).on('submit', '#magento_module_edit_form', function(e){
        e.preventDefault();
        var self = $(this);
        let formData = new FormData(document.getElementById("magento_module_edit_form"));
        var magento_module_id = $('#magento_module_edit_form #id').val();
        console.log(formData, magento_module_id);
        var button = $(this).find('[type="submit"]');
        console.log(button);
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