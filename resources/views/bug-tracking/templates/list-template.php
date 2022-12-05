<script type="text/x-jsrender" id="template-result-block">
	<div class="mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>ID</th>
                <th>Date</th>
                <th>Summary</th>
                <th>Type</th>
                <th>Steps to reproduce</th>
                <th>Environment</th>
                <th class='break'>Screenshot/Video url</th>
                <th>Created By</th>
                <th width="200px">Assign to</th>
                <th width="200px">Severity</th>
                <th width="200px">Status</th>
                <th>Module</th>
                <th width="300px">Communicaton</th>
                <th>Website</th>
                <th>Action</th>
		      </tr>
		    </thead>
		    <tbody class="pending-row-render-view infinite-scroll-pending-inner">
		    	{{props data}}
			      <tr>
			      	<td class='break'>{{:prop.id}}</td>
			      	<td class='break'>{{:prop.created_at}}</td>
			        <td class='break expand-row-msg' data-name="summary" id="copy" data-id="{{:prop.id}}"><span class="show-short-summary-{{:prop.id}}" onclick="copySumText()">{{:prop.summary_short}}</span>
                        <span class="show-full-summary-{{:prop.id}} hidden" >{{:prop.summary}}</span>
                    </td>
			        <td class='break'>{{:prop.bug_type_id}}</td>
			        <td class='break expand-row-msg' data-name="step_to_reproduce" data-id="{{:prop.id}}"><span class="show-short-Steps to reproduce-{{:prop.id}}">{{:prop.step_to_reproduce_short}}</span>
                        <span class="show-full-step_to_reproduce-{{:prop.id}} hidden" >{{:prop.step_to_reproduce}}</span>
                    </td>
			        <td class='break'>{{:prop.bug_environment_id}} {{:prop.bug_environment_ver}}</td>
			        <td class='break expand-row-msg' data-name="url" data-id="{{:prop.id}}">
			            <a href="{{:prop.url}}" target="_blank">
			                <span href="" class="show-short-url-{{:prop.id}}">{{:prop.url_short}}</span>
                            <span href="" class="show-full-url-{{:prop.id}} hidden" >{{:prop.url}}</span>
                        </a>

                        <button type="button"  class="btn btn-copy-url btn-sm" data-id="{{:prop.url}}" style="border:1px solid">
                            <i class="fa fa-clone" aria-hidden="true"></i></button>
                     </td>
                     <td class='break'>{{:prop.created_by}}</td>

			        <td class='break'>
			            <select class='form-control assign_to'  data-id="{{>prop.id}}" data-token=<?php echo csrf_token(); ?> >
			                <?php
                                foreach ($users as $user) {
                                    echo "<option {{if prop.assign_to == '".$user->id."'}} selected {{/if}} value='".$user->id."'>".$user->name.'</option>';
                                }
			            ?>
			            </select>
			            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
			        </td>
			        <td class='break'>
			           <select class='form-control bug_severity_id'  data-id="{{>prop.id}}" data-token=<?php echo csrf_token(); ?>>
			            <?php
			            foreach ($bugSeveritys as $bugSeverity) {
			                echo "<option {{if prop.bug_severity_id == '".$bugSeverity->id."'}} selected {{/if}} value='".$bugSeverity->id."'>".$bugSeverity->name.'</option>';
			            }
			            ?>
			            </select>
			        </td>
			        <td class='break'>
			            <select class='form-control bug_status_id'  data-id="{{>prop.id}}" data-token=<?php echo csrf_token(); ?>>
			                <?php
			                foreach ($bugStatuses as $bugStatus) {
			                    echo "<option {{if prop.bug_status_id == '".$bugStatus->id."'}} selected {{/if}} value='".$bugStatus->id."'>".$bugStatus->name.'</option>';
			                }
			            ?>
			            </select>
			            			            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>

			        </td>
			        <td class='break'>{{:prop.module_id}}</td>
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