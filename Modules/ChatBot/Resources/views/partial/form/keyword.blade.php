<div class="form-group">
	<label for="Keyword">Keyword</label>
	<?php echo Form::text("keyword",isset($keyword) ?: "", ["class" => "form-control" , "placeholder" => "Enter your keyword"]); ?>
</div>