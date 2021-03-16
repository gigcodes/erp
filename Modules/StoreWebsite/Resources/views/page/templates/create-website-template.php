<script type="text/x-jsrender" id="template-create-website">

    
        <div class="modal-content">
           
           <div class="modal-header">
              <h5 class="modal-title">{{if data.id}} Edit Page {{else}}Create Page{{/if}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
           </div>
          
            <form name="form-create-website" method="post">
            <?php echo csrf_field(); ?>
           
           <div class="modal-body">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="page">Copy Page</label>
                      <select name="page" class="form-control website-page-change">
                        <option value="">-- Select --</option>
                          <?php foreach($pages as $k => $page) { ?>
                            <option value="<?php echo $k; ?>"><?php echo $page; ?></option>
                          <?php } ?>
                      </select>  
                    </div>
                    <div class="form-group col-md-6">
                        <label for="language">Language</label>
                        <select name="language" class="form-control website-language-change">
                          <option value="">-- Select --</option>
                            <?php foreach($languages as $k => $language) { ?>
                              <option value="<?php echo $language; ?>"><?php echo $language; ?></option>
                            <?php } ?>
                        </select> 
                    </div>
                  </div>
              <div class="form-row">
                 {{if data}}
                    <input type="hidden" name="id" value="{{:data.id}}"/>
                 {{/if}}
              </div>
              <div class="form-row">
                  <div class="form-group col-md-6">
					  <div class="d-flex justify-content-between">
						  <label for="name">Title</label>
						  <button type="button" class="btn btn-primary btn-sm" id="keyword-search-btn"> <l class="fa fa-search"></i> </button>
					  </div>
					<div class="input-group">
						<input type="text" name="title" id="title-page" value="{{if data}}{{:data.title}}{{/if}}" class="form-control" placeholder="Enter title">
					</div>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="meta_title">Meta Title</label>
                    <input type="text" name="meta_title" value="{{if data}}{{:data.meta_title}}{{/if}}" class="form-control" id="meta_title" placeholder="Enter Meta title">
                  </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
				<div class="d-flex justify-content-between">
					<label for="meta_keywords">Meta Keywords</label>
					<span id="meta_keywords_count"></span>
				</div>
                  <input type="text" name="meta_keywords" value="{{if data}}{{:data.meta_keywords}}{{/if}}" class="form-control" id="meta_keywords" placeholder="Enter Keywords">
					  <div class="form-group">
						<div class="justify-content-end pt-4 input-group">
							<input type="text" value="" class="hide form-control  w-50" id="extra-keyword-search">
							<div class="input-group-append">
								<button type="button" class="hide btn btn-primary" id="extra-keyword-search-btn"> <l class="fa fa-search"></i> </button>
							</div>
						</div>
						<div class="row pt-3 height-fix suggestList" style="display:none">
							<table class="table table-bordered">
								<thead>
									<tr>
										<th>Keywords</th>
										<th>Avg. monthly</th>
										<th>Competition</th>
									</tr>
								</thead>
								<tbody class="suggestList-table"></tbody>
							</table>
						</div>
					   </div>
				   
                </div>
                <div class="form-group col-md-6">
					<div class="d-flex justify-content-between">
						<label for="meta_description">Meta Description</label>
						<span id="meta_desc_count"></span>
					</div>
                  <textarea name="meta_description" class="form-control" placeholder="Enter meta description">{{if data}}{{:data.meta_description}}{{/if}}</textarea>
                </div>
              </div>
              <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="content_heading">Content heading</label>
                    <input type="text" name="content_heading" value="{{if data}}{{:data.content_heading}}{{/if}}" class="form-control" id="content_heading" placeholder="Enter content_heading">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="platform_id">Platform ID</label>
                    <input type="text" name="platform_id" value="{{if data}}{{:data.platform_id}}{{/if}}" class="form-control" id="content_heading" placeholder="Enter Platform ID">
                  </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-12">
                  <label for="content">Content</label>
                  <textarea name="content" class="form-control content-preview" id="google_translate_element" placeholder="Enter content">{{if data}}{{:data.content}}{{/if}}</textarea>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="layout">layout</label>
                  <input type="text" name="layout" value="{{if data}}{{:data.layout}}{{/if}}" class="form-control" id="layout" placeholder="Enter layout">
                </div>
                <div class="form-group col-md-6">
                     <label for="active">Active</label>
                     <select name="active" class="form-control store-website-change">
                        <option value="">-- N/A --</option>
                        <?php
                            foreach([0 => "No", 1 => "Yes"] as $k => $l) {
                                echo "<option {{if data.active == '".$k."'}} selected {{/if}} value='".$k."'>".$l."</option>";
                            }
                        ?>
                     </select>
                  </div>
                  <div class="form-group col-md-6">
                     <label for="language">Language</label>
                     <select name="language" class="form-control store-website-language">
                        <option value="">-- N/A --</option>
                        <?php
                            foreach(\App\Language::pluck('name','name')->toArray() as $k => $l) {
                                echo "<option {{if data.language == '".$k."'}} selected {{/if}} value='".$k."'>".$l."</option>";
                            }
                        ?>
                     </select>
                  </div>
              </div>
              <div class="form-row">
                {{if data && data.id}}
                    <input type="text" name="stores_str" value="{{if data}}{{:data.stores}}{{/if}}" class="form-control" placeholder="Enter Stores comma seperate">
                {{else}}
                  <div class="form-group col-md-6">
                     <label for="store_website_id">Store website</label>
                     <select name="store_website_id" class="form-control store-website-change">
                        <option value="">-- N/A --</option>
                        <?php
                            foreach($storeWebsites as $k => $l) {
                                echo "<option {{if data.store_website_id == '".$k."'}} selected {{/if}} value='".$k."'>".$l."</option>";
                            }
                        ?>
                     </select>
                  </div>
                  <div class="form-group col-md-6">
                     <label for="stores">Stores</label>
                     <select name="stores[]" class="form-control store-selection" multiple="multiple">
                     </select>
                  </div>
                {{/if}}
              </div>
           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary submit-store-site">Save changes</button>
           </div>
          </form>
        </div>
</script> 