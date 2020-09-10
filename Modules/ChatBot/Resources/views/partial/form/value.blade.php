<div class="form-group">
	<label for="value">Intent</label>
	<?php echo Form::text("value",isset($value) ?: "", ["class" => "form-control" , "placeholder" => "Enter your value"]); ?>
</div>
<div class="form-group">
	<label for="value">Type</label>
	<select name="keyword_or_question" id="" class="form-control">
		<option value="question">Question</option>
		<option value="keyword">Keyword</option>
	</select>
</div>