<script type="text/x-jsrender" id="template-result-block">
{{if pageUrl == "store-website.page.keywords"}}
 <div class="table-responsive mt-3 keywordRecords">
        <table class="table table-bordered">
            <thead>
              <tr>
                <th width="5%">Title</th>
                <th width="10%">Url</th>
                <th width="5%">Store View</th>
                <th width="5%">Site</th>
                <th width="5%">Meta Title</th>
                <th width="5%">Meta Keyword</th>
                <th width="10%">Meta Description</th>
                <th width="30%">Keywords</th>
                <th width="25%">Actions</th>
              </tr>
            </thead>
            <tbody>
                {{props data}} 
                  <tr>
                    <td>{{:prop.title}} </td>
                    <td>{{:prop.url_key}}</td>
                    <td title="{{:prop.stores}}">{{:prop.stores_small}}</td>
                    <td>{{:prop.store_website_name}}</td>
                    <td>{{:prop.meta_title}}</td>
                    <td id="keyword_{{:prop.id}}">{{:prop.meta_keywords}}</td>
                    <td>{{:prop.meta_description}}</td>
                    <td id="col_{{:prop.id}}" data-id="{{:prop.id}}" class="row_keywords" data-title="{{:prop.title}}"></td>
                    <td>
                        <button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>
						<button type="button" title="Pull" data-id="{{>prop.id}}" class="btn btn-pull">
			        		<i class="fa fa-download" aria-hidden="true"></i>
			        	</button>
                        <button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="History" data-id="{{>prop.id}}" class="btn btn-find-history">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Language" data-id="{{>prop.id}}" class="btn btn-translate-for-other-language">
                            <i class="fa fa-language" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Activities" data-id="{{>prop.id}}" class="btn btn-activities">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </button>
                    </td>
                  </tr>
                {{/props}}
            </tbody>
        </table>
        {{:pagination}}
    </div>
	{{else}}
    <div class="table-responsive mt-3" {{:pageUrl}}>
        <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>Platform ID</th>
                <th>Title</th>
                <th>Url</th>
                <th>Store View</th>
                <th>Site</th>
                <th>Active</th>
                <th>Pushed</th>
                <th>Is latest version Pushed</th>
                <th>Is latest version translated</th>
                <th>Created at</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
                {{props data}} 
                  <tr>
                    <td><input type="checkbox" class="groups" name="groups[]" value="{{:prop.id}}">&nbsp;{{:prop.id}}</td>
                    <td><input type="text" value="{{:prop.platform_id}}" onchange="save_platform_id({{:prop.id}})"/></td>
                    <td>{{:prop.title}} </td>
                    <td><a href="#" onclick="openUrl('{{:prop.store_website_name}}/{{:prop.url_key}}')">{{:prop.url_key}}</a></td>
                    <td title="{{:prop.stores}}">{{:prop.stores_small}}</td>
                    <td>{{:prop.store_website_name}}</td>
                    <td>{{if prop.active == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{if prop.is_pushed == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{if prop.is_latest_version_pushed == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{if prop.is_latest_version_translated == "1"}}Yes{{else}}NO{{/if}}</td>
                    <td>{{:prop.created_at}}</td>
                    <td>
                        <button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
                            <i class="fa fa-edit" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Push" data-id="{{>prop.id}}" class="btn btn-push">
                            <i class="fa fa-upload" aria-hidden="true"></i>
                        </button>
						<button type="button" title="Pull" data-id="{{>prop.id}}" class="btn btn-pull">
			        		<i class="fa fa-download" aria-hidden="true"></i>
			        	</button>
                        <button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="History" data-id="{{>prop.id}}" class="btn btn-find-history">
                            <i class="fa fa-globe" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Language" data-id="{{>prop.id}}" class="btn btn-translate-for-other-language">
                            <i class="fa fa-language" aria-hidden="true"></i>
                        </button>
                        <button type="button" title="Activities" data-id="{{>prop.id}}" class="btn btn-activities">
                            <i class="fa fa-info-circle" aria-hidden="true"></i>
                        </button>
						 <button type="button" title="Pull logs" data-id="{{>prop.id}}" class="btn btn-pullLogs">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </td>
                  </tr>
                {{/props}}
            </tbody>
        </table>
        {{:pagination}}
    </div>
	{{/if}}
</script> 