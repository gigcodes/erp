<script type="text/x-jsrender" id="template-result-block">
<div class="mt-3">
	<table class="table table-bordered" id="script_document_maintable">
	    <thead>
	      	<tr>
				<th width="4%">ID</th>
				<th width="6%">Date</th>
				<th width="3%">File</th>
				<th width="5%">Description</th>
				<th width="8%">Usage Parameter</th>
				<th width="4%">Category</th>
				<th width="5%">Comments</th>
				<th width="10%">Author</th>
				<th width="3%">Action</th>
			</tr>
	    </thead>
    	<tbody class="pending-row-render-view infinite-scroll-pending-inner">
    		{{props data}}
				
		      	<tr>
			      	<td>{{:prop.id}}</td>

			      	<td>{{:prop.created_at_date}}</td>

			      	<td>{{:prop.file}}</td>

			      	<td class='break expand-row-msg' data-name="url" data-id="{{:prop.id}}">
						<button class="btn btn-sm upload-script_documents-files-button" type="button" title="Uploaded Files" data-script_document_id="{{:prop.id}}">
							<i class="fa fa-cloud-upload" aria-hidden="true"></i>
						</button>
						<button class="btn btn-sm view-script_documents-files-button" type="button" title="View Uploaded Files" data-script_document_id="{{:prop.id}}">
							<img src="/images/google-drive.png" style="cursor: nwse-resize; width: 12px;">
						</button>
                 	</td>

			      	<td>{{:prop.usage_parameter}}</td>

			      	<td>{{:prop.category}}</td>

			      	<td>{{:prop.comments}}</td>

			      	<td>{{:prop.author}}</td>

			        <td>		
					 	<div  class="d-flex">
							<button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
							</button>
							
							<button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
								<i class="fa fa-trash" aria-hidden="true"></i>
							</button>
					 	</div>
			        </td>
		      </tr>
		    {{/props}}  
	    </tbody>
	</table>
	{{:pagination}}
</div>
</script>
