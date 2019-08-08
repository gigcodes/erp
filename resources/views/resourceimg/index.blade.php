@extends('layouts.app')
@section('content')
<link href="{{ asset('css/treeview.css') }}" rel="stylesheet">
	<div class="container">
		<div class="row">
		  <div class="col-md-12">
		    <div class="panel panel-default">
		      <div class="panel-heading">Resources</div>
		      <div class="panel-body">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
                @if ($message = Session::get('danger'))
                    <div class="alert alert-danger alert-block">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <strong>{{ $message }}</strong>
                    </div>
                @endif
		        <h2>List Resources Center 
		        	<div class="btn-group pull-right">
		        	   	<button type="button" class="btn btn-image" title="Add Resource" data-toggle="modal" data-target="#addresource">
		        	   		<i class="fa fa-plus"></i>
		        	   	</button>
		        	</div>
		        </h2><hr>
		        <div class="table-responsive col-md-12">
		          <table class="table table-striped table-bordered" style="border: 1px solid #ddd;">
		            <thead>
		              <tr>
  		              	<th style="width: 2%;">#</th>
  		              	<th style="width: 20%;">Category</th>
  		              	<th style="width: 10%;">Url</th>
  		              	<th style="width: 10%;">Images</th>
  		              	<th style="width: 15%;">Created at</th>
  		              	<th style="width: 10%;">Created by</th>
  		              </tr>
		            </thead>
		            <tbody>
		            	 @if(count($allresources) > 0)
				            @foreach($allresources as $key => $resources)
				                <tr>
					                <td>{{($key+1)}}</td>
					                <td>{{$resources['cat']}}</td>
					                <td><a href="{{$resources['url']}}" title="View Url" target="_blank">Click Here</a></td>
					                <td><a href="{{ action('ResourceImgController@imagesResource', $resources['id']) }}" title="View Images">View</a></td>
		    		                <td>{{date("l, d/m/Y",strtotime($resources['updated_at']))}}</td>
		    		                <td>{{ucwords($resources['created_by'])}}</td>
		    		            </tr>
				            @endforeach
				         @else
				        	<tr>
				        		<td class="text-center" colspan="8">No Record found.</td>
				        	</tr>
				        @endif
		            </tbody>
		          </table>
		        </div>
		      </div>
		    </div>
		  </div>
		</div>
	</div>

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
				  		                	{!! Form::label('Category:') !!}
				  		                	<div class="input-group {{ $errors->has('cat_id') ? 'has-error' : '' }}">
					  		                	<?=@$Categories?>
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

	<div class="modal fade" id="addcategory" tabindex="-1" role="dialog">
	   	<div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	{!! Form::open(['route'=>'add.resourceCat']) !!}
					<div class="modal-header">
						<h2 class="modal-title" style="font-size: 24px;">Create Resource Category</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-4">
				                <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
				                    <label>Category List</label>
				                    <ul id="tree1">
				                        @foreach($categories as $category)
				                            <li>
				                                {{ $category->title }} ({{$category->id}})
				                                @if(count($category->childs))
				                                    @include('category.manageChild',['childs' => $category->childs])
				                                @endif
				                            </li>
				                        @endforeach
				                    </ul>
				                </div>
							</div>
							<div class="col-md-8">
				                <div class="row">
				                	<div class="col-md-12">
  		                                <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
  		                                    {!! Form::label('Parent Category:') !!}
  		                	                <?=@$Categories?>
  		                                    <span class="text-danger">{{ $errors->first('parent_id') }}</span>
  		                                </div>
				                	</div>
				                	<div class="col-md-12">
				  		                <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
				  		                    {!! Form::label('Category Name:') !!}
				  			                <input type="text" name="title" class="form-control" required placeholder="Create Category">
				  		                    <span class="text-danger">{{ $errors->first('title') }}</span>
				  		                </div>
				                	</div>
				                </div>
				            </div>
						</div>
				    </div>
				    <div class="modal-footer">
			            <button type="submit" class="btn btn-secondary"><i class="fa fa-plus"></i></button>
				        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i></button>
				    </div>
				{!! Form::close() !!}
			</div>	
	  	</div>
	</div>
	<div class="modal fade" id="editcategory" tabindex="-1" role="dialog">
	   	<div class="modal-dialog modal-lg">
		    <div class="modal-content">
		    	{!! Form::open(['route'=>'edit.resourceCat']) !!}
					<div class="modal-header">
						<h2 class="modal-title" style="font-size: 24px;">Edit Resource Category</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					</div>
				    <div class="modal-body">
						<div class="row">
							<div class="col-md-12">
				                <div class="row">
				                	<div class="col-md-8 col-md-offset-2">
  		                                <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : '' }}">
  		                                    {!! Form::label('Category:') !!}
  		                	                <?=@$Categories?>
  		                                    <span class="text-danger">{{ $errors->first('parent_id') }}</span>
  		                                </div>
				                	</div>
				                </div>
				            </div>
						</div>
				    </div>
				    <div class="modal-footer">
				    	<button type="submit" name="type" value="edit" class="btn btn-image"><i class="fa fa-pencil"></i></button>
                        <button type="submit" name="type" value="delete" class="btn btn-image"><i class="fa fa-trash"></i></button>
				        <button type="button" class="btn btn-image" data-dismiss="modal"><i class="fa fa-times"></i></button>
				    </div>
				{!! Form::close() !!}
			</div>	
	  	</div>
	</div>

	<script type="text/javascript">
		function PasteImage(){var e=document.getElementById("my_canvas").toDataURL();$("#cpy_img").val(e),$("#save_img").fadeIn(200),$(".msg").empty(),$(".msg").css("color","green"),$(".msg").text("Image Loaded Successfully."),$(".can_id").attr("placeholder","Image Loaded Successfully, Paste another to change."),$("#src_img").attr("src",e)}var CLIPBOARD=new CLIPBOARD_CLASS("my_canvas",!0);function CLIPBOARD_CLASS(e,t){var a=this,n=document.getElementById(e),i=document.getElementById(e).getContext("2d");document.addEventListener("paste",function(e){"can_id"==e.target.id&&(console.log(e),a.paste_auto(e))},!1),this.paste_auto=function(e){if(e.clipboardData){var t=e.clipboardData.items;if(!t)return;for(var a=!1,n=0;n<t.length;n++)if($("#cpy_img").val(""),-1!==t[n].type.indexOf("image")){var i=t[n].getAsFile(),c=(window.URL||window.webkitURL).createObjectURL(i);this.paste_createImage(c),a=!0}1==a?(e.preventDefault(),$(".msg").text("Image Loading, Please Wait."),$(".msg").css("color","red"),setTimeout(PasteImage,5e3)):(e.preventDefault(),$(".can_id").attr("placeholder","Please paste only image."))}},this.paste_createImage=function(e){var a=new Image;a.onload=function(){1==t?(n.width=a.width,n.height=a.height):i.clearRect(0,0,n.width,n.height),i.drawImage(a,0,0)},a.src=e}}
	</script>
	<script src="{{asset('js/treeview.js')}}"></script>
@endsection