<script type="text/x-jsrender" id="template-result-block">
	<div class="mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>ID</th>
                <th>Date</th>
                <th>Name</th>
                <th>Suite</th>
                <th>Created By</th>
                <th>Module</th>
                <th>Precondition</th>
                <th width="200px">Assign to</th>
                <th width="200px">Step To Reproduce</th>
                <th>Expected Result</th>
                <th width="200px">Status</th>
                <th width="300px">Communication</th>
                <th>Website</th>
                <th>Action</th>
		      </tr>
		    </thead>
		    <tbody class="pending-row-render-view infinite-scroll-pending-inner">
		    	{{props data}}
			      <tr>
                    <td class='break'>{{:prop.id}}</td>
			      	<td class='break'>{{:prop.created_at}}</td>
			        <td class='break' >{{:prop.name}}</td>
			        <td class='break'>{{:prop.suite}}</td>
			        <td class='break'>{{:prop.created_by}}</td>
			        <td class='break'>{{:prop.module_id}}</td>
			        <td class='break'>{{:prop.precondition}}</td>
			        <td class='break'>
			            <select class='form-control assign_to'  data-id="{{>prop.id}}" data-token=<?php echo csrf_token();?> >
			                <?php
                                foreach ($users as $user) {
                                    echo "<option {{if prop.assign_to == '".$user->id."'}} selected {{/if}} value='".$user->id."'>".$user->name.'</option>';
                                }
                            ?>
			            </select>
			        </td>
			        <td class='break expand-row-msg' data-name="step_to_reproduce" data-id="{{:prop.id}}"><span class="show-short-Steps to reproduce-{{:prop.id}}">{{:prop.step_to_reproduce_short}}</span>
                        <span class="show-full-step_to_reproduce-{{:prop.id}} hidden" >{{:prop.step_to_reproduce}}</span>
                    </td>
			        <td class='break'>{{:prop.expected_result}}</td>
			        <td class='break'>
			            <select class='form-control test_case_status_id'  data-id="{{>prop.id}}" data-token=<?php echo csrf_token();?>>
			                <?php
                                foreach ($testCaseStatuses as $testCaseStatus) {
                                    echo "<option {{if prop.test_case_status_id == '".$testCaseStatus->id."'}} selected {{/if}} value='".$testCaseStatus->id."'>".$testCaseStatus->name.'</option>';
                                }
                            ?>
			            </select>
			        </td>

			        <td class='break'>
			         <div style="margin-bottom:10px;width: 100%;">
                    <div class="d-flex">
                       <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" id="getMsg{{>prop.id}}" name="message" placeholder="Message" value=""><div style="max-width: 30px;">
                       <button class="btn btn-sm btn-image send-message" title="Send message" data-id="{{>prop.id}}"><img src="images/filled-sent.png" /></button> </div>
                        <div style="max-width: 30px;">
                        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='bug' data-id="{{:prop.id}}" title="Load messages"><img src="images/chat.png" alt=""></button>
                         </div>
                        </div>
                    </div>
			        </td>
			        <td class='break'>{{:prop.website}}</td>
			        <td>
			        <div class="d-flex">
			        	<button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Push"  data-id="{{:prop.id}}" class="btn btn-push">
			        	<i class="fa fa-eye" aria-hidden="true"></i>
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