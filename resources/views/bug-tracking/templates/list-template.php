<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>ID</th>
                <th>Summary</th>
                <th>Type of Bug</th>
                <th>Steps to reproduce</th>
                <th>Environment</th>
                <th> Screenshot/Video url</th>
                <th>Created By</th>
                <th>Assign to</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Module/Feature</th>
                <th>Remarks </th>
                <th>Website</th>
                <th width="200px">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			        <td class='break expand-row-msg' data-name="summary" data-id="{{:prop.id}}"><span class="show-short-summary-{{:prop.id}}">{{:prop.summary_short}}</span>
                        <span class="show-full-summary-{{:prop.id}} hidden" >{{:prop.summary}}</span>
                    </td>
			        <td class='break'>{{:prop.bug_type_id}}</td>
			       <td class='break expand-row-msg' data-name="step_to_reproduce" data-id="{{:prop.id}}"><span class="show-short-Steps to reproduce-{{:prop.id}}">{{:prop.step_to_reproduce_short}}</span>
                        <span class="show-full-step_to_reproduce-{{:prop.id}} hidden" >{{:prop.step_to_reproduce}}</span>
                    </td>
			        <td class='break'>{{:prop.bug_environment_id}}</td>
			        <td class='break expand-row-msg' data-name="url" data-id="{{:prop.id}}"><span class="show-short-url-{{:prop.id}}">{{:prop.url_short}}</span>
                         <span class="show-full-url-{{:prop.id}} hidden" >{{:prop.url}}</span>
                     </td>
                     <td class='break'>{{:prop.created_by}}</td>

			        <td class='break'>
			        <select class='form-control assign_to'  data-id="{{>prop.id}}">
			        <?php
                         foreach($users as $user){
                             echo "<option {{if prop.assign_to == '".$user->id."'}} selected {{/if}} value='".$user->id."'>".$user->name."</option>";
                         }
                         ?>
			        </select>
			        </td>
			        <td class='break'>
			        <select class='form-control bug_severity_id'  data-id="{{>prop.id}}">
			        <?php
                     foreach($bugSeveritys as $bugSeverity){
                    echo "<option {{if prop.bug_severity_id == '".$bugSeverity->id."'}} selected {{/if}} value='".$bugSeverity->id."'>".$bugSeverity->name."</option>";
                            }
                    ?>
			        </select>
			        </td>
			        <td class='break'>
			        <select class='form-control bug_status_id'  data-id="{{>prop.id}}">
			        <?php
                         foreach($bugStatuses as $bugStatus){
                            echo "<option {{if prop.bug_status_id == '".$bugStatus->id."'}} selected {{/if}} value='".$bugStatus->id."'>".$bugStatus->name."</option>";
                             }
                     ?>
			        </select>
			        </td>
			        <td class='break'>{{:prop.module_id}}</td>
			        <td class='break'>{{:prop.remark}}</td>
			        <td class='break'>{{:prop.website}}</td>

			        <td>
			        	<button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Push"  data-id="{{:prop.id}}" class="btn btn-push">
			        	<i class="fa fa-eye" aria-hidden="true"></i>
			        	</button>

			        	<button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
	<div id="bugtrackingShowFullTextModel" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title">Full text view</h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body bugtrackingmanShowFullTextBody">

          </div>
        </div>
      </div>
    </div>
  </div>
</script>