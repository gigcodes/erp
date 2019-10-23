<div class="modal fade" id="addresource" tabindex="-1" role="dialog">
	   	<div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	{!! Form::open(['route'=>'add.resource','files' => true]) !!}
					<div class="modal-header">
						<h2 class="modal-title" style="font-size: 24px;">Create Resource Center</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
				                <div class="row">
				                	<div class="col-md-8 col-md-offset-2">
				  		                <div class="form-group">
				  		                	
				  		                	<div class="input-group {{ $errors->has('cat_id') ? 'has-error' : '' }}">
					  		                	
					  		                	<span class="input-group-btn">
	            		  		                    <button type="button" class="btn btn-image" title="Add Category" data-toggle="modal" data-target="#addcategory">
	            					        			<i class="fa fa-plus"></i>
	            					        	   	</button>
	            					        	   	<button type="button" class="btn btn-image" title="Edit Category" data-toggle="modal" data-target="#editcategory">
	            					        			<i class="fa fa-pencil"></i>
	            					        	   	</button>
					  		                	</span>
				  		                	</div>
				  		                    <span class="text-danger">{{ $errors->first('cat_id') }}</span>
				  		                </div>
				                	</div>
				                </div>
				                <div class="row">
	        	                	<div class="col-md-8 col-md-offset-2">
	        			                <div class="form-group {{ $errors->has('image1') ? 'has-error' : '' }}">
	        			                    {!! Form::label('Upload Image:') !!}
	        			                    <input type="file" name="image1" placeholder="Upload Image">
	        			                    <span class="text-danger">{{ $errors->first('image1') }}</span>
	        			                </div>
	        			            </div>
				                </div>
				                <div class="row">
	        	                	<div class="col-md-8 col-md-offset-2">
				  		                <div class="form-group">
					  		                {!! Form::label('Paste Image:') !!}
					  		                <textarea class="form-control can_id" id="can_id" placeholder="Paste Image" style="resize: none"></textarea>
					  		                <span class="msg"></span>
					  		                <img src="" id="src_img" style="width:100%">
				  		                </div>
				                	</div>
	        	                </div>
				                <div class="row">
						        	<div class="col-md-8 col-md-offset-2">
				  		                <div class="form-group">
					  		                {!! Form::label('Url:') !!}
					  		                <input type="text" class="form-control" name="url" placeholder="Resource Url">
				  		                </div>
				                	</div>
				                </div>
				                <div class="row">
				                	<div class="col-md-8 col-md-offset-2">
				  		                <div class="form-group">
					  		                {!! Form::label('Description:') !!}
					  		                <textarea class="form-control" name="description" placeholder="Resource Description"></textarea>
				  		                </div>
				                	</div>
				                </div>
				            </div>
						</div>
				    </div>
				    <div class="modal-footer">
			            <canvas style="border:none;display: none;" id="my_canvas"></canvas>
				    	<input type="hidden" autocomplete="off" class="form-control" name="image2" id="cpy_img">
			            <button type="submit" style="display: block;" id="save_img" class="btn btn-secondary"><i class="fa fa-plus"></i></button>
				        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i></button>
				    </div>
				{!! Form::close() !!}
			</div>	
	  	</div>
	</div>