<script type="text/x-jsrender" id="template-result-block">
<div class="mt-3">
	<table class="table table-bordered" id="script_document_maintable">
	    <thead>
	      	<tr>
				<th width="4%">ID</th>
				<th width="8%">Date</th>
				<th width="8%">Sender</th>
				<th width="8%">Receiver</th>
			</tr>
	    </thead>
    	<tbody class="pending-row-render-view infinite-scroll-pending-inner">
    		{{props data}}
				
		      	<tr>
			      	<td>{{:prop.id}}</td>

			      	<td>{{:prop.created_at_date}}</td>

			      	<td>{{:prop.user.name}}</td>

			      	<td>{{:prop.userrequest.name}}</td>

               	</tr>
		    {{/props}}  
	    </tbody>
	</table>
	{{:pagination}}
</div>
</script>
