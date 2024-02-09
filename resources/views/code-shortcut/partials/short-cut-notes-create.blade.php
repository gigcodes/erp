 <!-- Platform Modal content-->
	 <div id="code-shortcut-platform" class="modal fade in" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">
			  <div class="modal-header">
				<h4 class="modal-title">Add Platform</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			  </div>
				<form action="{{route('code-shortcuts.platform.store')}}" method="POST" id="code-shortcut-platform-form">
					@csrf
					  <div class="modal-body">
						  <div class="form-group">
							  {!! Form::label('platform_name', 'Name', ['class' => 'form-control-label']) !!}
							  {!! Form::text('platform_name', null, ['class'=>'form-control','required','rows'=>3]) !!}
						</div>
						<div class="modal-footer">
						  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						  <button type="submit" class="btn btn-primary">Save</button>
					  </div>
					</div>
				</form>
			</div>

		</div>
	</div>


     <!-- code Modal content-->

    <div class="modal fade" id="create_code_shortcut" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Create Code Shortcut</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" enctype="multipart/form-data" id="code-shortcut-from" action="{{route('code-shortcuts.store')}}">
                    @csrf
                    <div class="modal-body">

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Supplier</label>
                                    <?php echo Form::select("supplier",['' => ''],null,["class" => "form-control globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.shortcutsuplliers'), 'data-placeholder' => 'supplier']); ?>

                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Platform</label>
                                <?php echo Form::select("platform_id",['' => ''],null,["class" => "form-control globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.shortcutplatform'), 'data-placeholder' => 'Platforms']); ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Folder Name</label>
                                <?php echo Form::select("folder_id",['' => ''],null,["class" => "form-control globalSelect2" ,"style" => "width:100%;", 'data-ajax' => route('select2.shortcutfolders'), 'data-placeholder' => 'Folders']); ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <strong>Upload File</strong>
                                <input type="file" name="notesfile" id="shortnotefileInput" >
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Code</label>
                                <?php echo Form::text('code', null, ['class' => 'form-control code', 'value' => "{{old('code')}}"]); ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Title</label>
                                <?php echo Form::text('title', null, ['class' => 'form-control title',  'value' => "{{old('title')}}"]); ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Solution</label>
                                <?php echo Form::text('solution', null, ['class' => 'form-control solution', 'value' => "{{old('solution')}}"]); ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Description</label>
                                <?php echo Form::text('description', null, ['class' => 'form-control description', 'value' => "{{old('description')}}"]); ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id="show_full_log_modal">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Full Log</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12" id="show_full_log_modal_content">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
