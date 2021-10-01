<form method="POST" action="<?php echo route("save.leads.categories"); ?>" id="lead_create_brands" enctype="multipart/form-data" >
  
  <?php echo csrf_field(); ?>
  
   <div class="form-group">
    <label for="categories">Category:</label>
    <select placeholder="Brand" class="form-control lead_brand multi_lead_status input-size" name="categories[]" multiple="" style="width: 150px; border-radius: 2px;">
        <option disabled="true" >Select Cartegory</option>
        @foreach($categories as $category)
          <option value="{{$category['id']}}">{{ $category['title'] }}</option>
        @endforeach
    </select>
  </div> 
  
  <button type="submit" class="btn btn-default lead-button-submit-for-category-brand">Submit</button>
</form>