<div id="categoryCreateModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
  
      <!-- Modal content-->
      <div class="modal-content">
        <form action="{{ route('supplier/add/category') }}" method="POST">
			@csrf
	
			<div class="modal-header">
				<h4 class="modal-title">Add Supplier Category</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<label>Name:</label>
					<input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
					@if($errors->has('name'))<p style="color: red;">{{ $errors->first('name') }}</p>@endif
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="submit" class="btn btn-secondary">Add</button>
			</div>
        </form>
      </div>
  
    </div>
  </div>