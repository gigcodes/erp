<div class="form-group">
	<label for="value">Intent / Entity</label>
	<?php echo Form::text("value",isset($value) ?: "", ["class" => "form-control" , "placeholder" => "Enter your value"]); ?>
</div>
<div class="form-group">
	<label for="value">Category</label>
	<select name="category_id" id="" class="form-control">
		<option value="">Select</option>
		@foreach($allCategoryList as $cat)
		<option value="{{$cat['id']}}">{{$cat['text']}}</option>
		@endforeach
	</select>
</div>
<div class="form-group">
	<label for="value">Type</label>
	<select name="keyword_or_question" id="" class="form-control view_details_div">
		<option value="intent">Intent</option>
		<option value="entity">Entity</option>
		<option value="erp">ERP</option>
	</select>
</div>
<div id="intent_details">
		<div class="form-group">
		<label for="question">User Intent</label>
			<div class="row align-items-end" id="intentValue_1">
				<div class="col-md-9">
					<?php echo Form::text("question[]", null, ["class" => "form-control", "placeholder" => "Enter User Intent"]); ?>
				</div>
				<div class="col-md-2" id="add-intent-value-btn">
					<!-- <a href="javascript:;" class="btn btn-secondary btn-sm add-more-intent-condition-btn">
						-
					</a>	 -->
				</div>
			</div>
		</div>
		<div class="form-group" id="add-intent-value-btn">
					<a href="javascript:;" class="btn btn-secondary btn-sm add-more-intent-condition-btn">
						<span class="glyphicon glyphicon-plus"></span>
					</a>	
		</div>
</div>
<div id="entity_details">
<div class="form-group">
	<label for="value">User Entity</label>
	<?php echo Form::text("value_name", null, ["class" => "form-control", "id" => "value", "placeholder" => "Enter user entity"]); ?>
</div>
<div class="form-row align-items-end">
					    <div class="form-group col-md-4">
						    <label for="type">Type</label>
						    <?php echo Form::select("types",["synonyms" => "synonyms", "patterns" => "patterns"] ,null, ["class" => "form-control", "id" => "types"]); ?>
					    </div>
						<div class="form-group col-md-4">
							<div class="row align-items-end" id="typeValue_1">
								<div class="col-md-9">
									<?php echo Form::text("type[]", null, ["class" => "form-control", "id" => "type", "placeholder" => "Enter value", "maxLength"=> 64]); ?>
								</div>
							</div>
						</div>
						<div class="form-group col-md-2" id="add-type-value-btn">
				  	        <a href="javascript:;" class="btn btn-secondary btn-sm add-more-condition-btn">
			                    <span class="glyphicon glyphicon-plus"></span>
			                </a>	
			      	    </div>
					</div>
</div>
<div id="erp_details">
erp
</div>
<div class="form-group">
	<label for="value">Push to</label>
	<select name="erp_or_watson" id="" class="form-control">
		<option value="watson">Watson</option>
		<option value="erp">ERP</option>
	</select>
</div>