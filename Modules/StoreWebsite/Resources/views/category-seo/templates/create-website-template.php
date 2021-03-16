<script type="text/x-jsrender" id="template-create-website">
    
        <div class="modal-content">
           
           <div class="modal-header">
              <h5 class="modal-title">{{if data.id}} Edit Category SEO {{else}}Create Category SEO{{/if}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
          
            <form name="form-create-website" method="post">
            <?php echo csrf_field(); ?>
           
              <div class="modal-body">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="category_id">Category</label>
                      <select name="category_id" class="form-control">
                        <option value="">-- Select --</option>
                          <?php foreach($categories as $category) { ?>
                            <option {{if data.category_id == '<?php echo $category->id; ?>'}} selected {{/if}} value="<?php echo $category->id; ?>"><?php echo $category->title; ?></option>
                          <?php } ?>
                      </select>  
                    </div>
                    <div class="form-group col-md-6">
                      <label for="store_website_id">Store Website</label>
                      <select name="store_website_id" class="form-control">
                        <option value="">-- Select --</option>
                          <?php foreach($store_list as $k => $store) { ?>
                            <option {{if data.store_website_id == '<?php echo $k; ?>'}} selected {{/if}} value="<?php echo $k; ?>"><?php echo $store; ?></option>
                          <?php } ?>
                      </select>  
                    </div>
                     {{if data}}
                        <input type="hidden" name="id" value="{{:data.id}}"/>
                     {{/if}}
              </div>
              <div class="form-row">
                  <div class="form-group col-md-6">
                    <div class="input-group">
						<div class="d-flex justify-content-between">
							<label for="meta_title">Meta Title</label>
							<button type="button" class="btn btn-primary btn-sm" id="keyword-search-btn"> <l class="fa fa-search"></i> </button>
						</div>
						<input type="text" name="meta_title" value="{{if data}}{{:data.meta_title}}{{/if}}" class="form-control" id="meta_title" placeholder="Enter Meta title">
					</div>
                  </div>
                  <div class="form-group col-md-6">
					<div class="d-flex justify-content-between">
						<label for="meta_keywords">Meta Keywords</label>
						<span id="meta_keywords_count">  </span>
					</div>
                  <input type="text" name="meta_keyword" value="{{if data}}{{:data.meta_keyword}}{{/if}}" class="form-control" id="meta_keywords" placeholder="Enter Keywords">
				  		<div class="form-group ">
							<div class="justify-content-end pt-4 input-group">
								<input type="text" value="" class="hide form-control  w-50" id="extra-keyword-search">
								<div class="input-group-append">
									<button type="button" class="hide btn btn-primary input-group-text" id="extra-keyword-search-btn"> <l class="fa fa-search"></i> </button>
								</div>
							</div>
							<div class="row pt-3 height-fix suggestList" style="display:none">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>Keywords</th>
											<th>Search Volume</th>
											<th>Competition</th>
										</tr>
									</thead>
									<tbody class="suggestList-table"></tbody>
								</table>
							</div>
					   </div>
                </div>
              </div>
              <div class="row">
                <div class="form-group col-md-6">
					<div class="d-flex justify-content-between">
						<label for="meta_description">Meta Description</label>
						<span id="meta_desc_count"></span>
					</div>
                  <textarea name="meta_description" class="form-control" placeholder="Enter meta description">{{if data}}{{:data.meta_description}}{{/if}}</textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="language">Language</label>
                    <select name="language_id" class="form-control website-language-change">
                      <option value="">-- Select --</option>
                        <?php foreach($languages as $k => $language) { ?>
                          <option value="<?php echo $k; ?>"><?php echo $language; ?></option>
                        <?php } ?>
                    </select> 
                </div>
              </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary submit-store-category-seo">Save changes</button>
           </div>
          </form>
        </div>
</script> 