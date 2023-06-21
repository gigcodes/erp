<script type="text/x-jsrender" id="template-result-block">
	<div class="mt-3">
		<table class="table table-bordered" id="bug_tracking_maintable">
		    <thead>
		      <tr>
		      	<th width="4%"><input type="checkbox" id="chkBugChangeCommon" name="chkBugChangeCommon" class="chkBugChangeCommon"> ID</th>
                <th width="6%">Date</th>
                <th width="3%">Summary</th>
                <th width="5%">Type</th>
                <th width="8%">Steps to reproduce</th>
                <th width="4%">Environment</th>
                <th width="4%">Expected Result</th>
                <th class='break' width="7%">Screenshot / Video url</th>
                <th width="5%">Created By</th>
                <th width="10%">Assign to</th>
                <th width="8%">Severity</th>
                <th width="10%">Status</th>
                <th width="7%">Module</th>
                <th width="14%">Communicaton</th>
                <th width="4%">Website</th>
                <th width="3%">Action</th>
		      </tr>
		    </thead>
		    <tbody class="pending-row-render-view infinite-scroll-pending-inner">
		    	{{props data}}
				
			      <tr style="background-color:{{:prop.bug_color}}">
			      	<td><input type="checkbox" id="chkBugChange{{>prop.id}}" name="chkBugNameChange[]" class="chkBugNameClsChange"  value="{{>prop.id}}"></br> {{:prop.id}}</td>
			      	<td>{{:prop.created_at_date}}</td>
			        <td class='break expand-row' data-name="summary" id="copy" data-id="{{:prop.id}}" data-toggle="tooltip"><span class=" td-mini-container show-short-summary-{{:prop.id}}" onclick="copySumText()">{{:prop.summary_short}}</span>
                        <span class="td-full-container show-full-summary-{{:prop.id}} hidden" >{{:prop.summary}}</span>
                    </td>
			        <td class='break'  data-bug_type="{{:prop.bug_type_id_val}}">					
					<?php if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Lead Tester')) { ?>
						<select class='form-control bug_type_in_row'  data-id="{{>prop.id}}" style="padding:0px;" data-token=<?php echo csrf_token(); ?>  >
							<option value="">Select BugType</option>											
							<?php
                                foreach ($bugTypes as $bugtype) {
                                    echo "<option {{if prop.bug_type_id_val == '" . $bugtype->id . "'}} selected {{/if}} value='" . $bugtype->id . "'>" . $bugtype->name . '</option>';
                                }
					    ?>

						</select>
					<?php } else { ?>
					{{:prop.bug_type_id}}
					<?php } ?>	
					
					</td>
			        <td class='break expand-row' data-name="step_to_reproduce" data-id="{{:prop.id}}"  data-toggle="tooltip">
						<div class="td-mini-container td-mini-container-{{:prop.id}}">{{>prop.step_to_reproduce_short}}</div>
                        <div class="td-full-container td-full-container-{{:prop.id}} hidden" >{{:prop.step_to_reproduce}}</div>
                    </td>
			        <td class='break'>{{:prop.bug_environment_id}} {{:prop.bug_environment_ver}}</td>
			        <td class='break'>{{:prop.expected_result}}</td>

			        <td class='break expand-row-msg' data-name="url" data-id="{{:prop.id}}">
						<button class="btn btn-sm upload-bugs-files-button" type="button" title="Uploaded Files" data-bug_id="{{:prop.id}}">
							<i class="fa fa-cloud-upload" aria-hidden="true"></i>
						</button>
						<button class="btn btn-sm view-bugs-files-button" type="button" title="View Uploaded Files" data-bug_id="{{:prop.id}}">
							<img src="/images/google-drive.png" style="cursor: nwse-resize; width: 12px;">
						</button>
                     </td>
                     <td class='break'>{{:prop.created_by}}</td>

			        <td class='break'>
						<div class="d-flex">
							<select class='form-control assign_to'  data-id="{{>prop.id}}" style="padding:0px;" data-token=<?php echo csrf_token(); ?>  >
								<?php
					                foreach ($users as $user) {
					                    echo "<option {{if prop.assign_to == '" . $user->id . "'}} selected {{/if}} value='" . $user->id . "'>" . $user->name . '</option>';
					                }
					?>
							</select>
							<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
						</div>
			        </td>
			        <td class='break'>
						<div class="d-flex">
						   <select class='form-control bug_severity_id' id="bug_severity_id_{{>prop.id}}"   data-id="{{>prop.id}}" style="padding:0px;" data-token=<?php echo csrf_token(); ?>>
						   <option value="">-Select-</option>
							<?php
					foreach ($bugSeveritys as $bugSeverity) {
					    echo "<option {{if prop.bug_severity_id == '" . $bugSeverity->id . "'}} selected {{/if}} value='" . $bugSeverity->id . "'>" . $bugSeverity->name . '</option>';
					}
					?>
							</select>
							<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-severity-history" title="Show Severity History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
						</div>
			        </td>
			        <td class='break'>
						<div class="d-flex">
							<select class='form-control bug_status_id'  data-id="{{>prop.id}}" style="padding:0px;" data-token=<?php echo csrf_token(); ?>>
								<?php
					    foreach ($bugStatuses as $bugStatus) {
					        echo "<option {{if prop.bug_status_id == '" . $bugStatus->id . "'}} selected {{/if}} value='" . $bugStatus->id . "'>" . $bugStatus->name . '</option>';
					    }
					?>
							</select>
							<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
						</div>

			        </td>
         <td class='break'>	
					<?php if (Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Lead Tester')) { ?>
						<select class='form-control bug_module_in_row'  data-id="{{>prop.id}}" data-module="{{>prop.module_id}}" style="padding:0px;" data-token=<?php echo csrf_token(); ?>  >
							<option value="">Select Module</option>											
							<?php
					    foreach ($filterCategories as  $filterCategory) {
					        ?>

									<?php
					        echo "<option data-val='" . str_replace("'", '', $filterCategory) . "'  {{if prop.module_id == '" . str_replace("'", '', $filterCategory) . "'}} selected {{/if}} value='" . htmlentities($filterCategory) . "'>" . $filterCategory . '</option>';
					    }
					    ?>
						</select>
					<?php } else { ?>
					{{:prop.module_id}}
					<?php } ?>	
									
					</td>
			        <td class='break'>
			         <div style="margin-bottom:10px;width: 100%;">
                    <div class="d-flex">
                       <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" data-bid="{{>prop.id}}" id="getMsg{{>prop.id}}" name="message" placeholder="Message" value=""><div style="max-width: 30px;">
                       <button class="btn btn-sm btn-image send-message" title="Send message" data-id="{{>prop.id}}"><img src="images/filled-sent.png" data-id="{{>prop.id}}"  /></button> </div>
                        
                        </div>
						 <div class="d-flex">
							<div style="margin-bottom:10px;width: 100%;">
								<div class="d-flex justify-content-between expand-row-msg-chat" data-id="{{>prop.id}}">
									<span class="td-mini-container-{{>prop.id}} text-danger" style="margin:0px;">
										{{>prop.last_chat_message_short}}
									</span>
								</div>
								<div class="expand-row-msg-chat" data-id="{{>prop.id}}">
									<span class="td-full-container-{{>prop.id}} hidden text-danger">
										{{>prop.last_chat_message_long}}
									</span>
								</div>
							</div>
							
							<div style="max-width: 100%;text-align: right;padding-top: 10px;">
								<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='bug' data-id="{{:prop.id}}" title="Load messages"><img src="images/chat.png" alt=""></button>
							 </div>
						 </div>
                    </div>
			        </td>
			        <td>{{:prop.website}}</td>
			        <td>
						
			       
						<div  class="d-flex" style="margin-left:5px;">
							<input type="checkbox" id="chkBug{{>prop.id}}" data-user="{{>prop.assign_to}}" name="chkBugName" class="chkBugNameCls"  value="{{>prop.id}}">					 
							 <button  title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="{{>prop.id}}"  data-category_title="{{:prop.module_id}}"  data-module_id="{{:prop.module_id}}" data-website_id="{{:prop.website_id_val}}"  data-website="{{:prop.website}}" data-bug_type_id="{{:prop.bug_type_id_val}}" data-title="{{:prop.website}} - {{:prop.module_id}}"><img style="width:12px !important;margin-left:5px;" src="/images/add.png" /></button>
							 
							 <button type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="{{:prop.id}}" data-category="297"><i class="fa fa-info-circle"></i></button>
						 </div>
						
						 <div  class="d-flex">
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
