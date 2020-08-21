<script type="text/x-jsrender" id="template-result-block">
	<div class="row page-template-{{:page}}">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th width="2%">#</th>
		      	<th width="5%">Customer Name</th>
		        <th width="15%">Next Action</th>
		        <th width="10%">Communication box</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			      	<td>
		      		    <div class="row">
                           {{:prop.name}}
                        </div>
                        <div class="row">
                        	{{if prop.do_not_disturb == 1}}
                        		<a class="btn btn-image cls_dnt_btn do_not_disturb" href="javascript:;" data-id="{{:prop.id}}" data-user-id="">
                            		<img src="/images/do-not-disturb.png" />
                        		</a>
                        	{{else}}
                        		<a class="btn btn-image cls_dnt_btn do_not_disturb" href="javascript:;" data-id="{{:prop.id}}" data-user-id="">
                                	<img src="/images/do-disturb.png" />
                            	</a>
                        	{{/if}}
                            <a class="btn btn-image  create-customer-related-task" title="Task" href="javascript:;" data-id="{{:prop.id}}" data-user-id="">
                            	<i class="fa fa-plus" aria-hidden="true"></i>
                            </a>
                            <a class="btn btn-image count-customer-tasks" title="Task Count" href="javascript:;" data-id="{{:prop.id}}" data-user-id="">
                            	<img src="/images/remark.png" />
                            </a>
                        </div>
			        </td>
			        <td>
			        	<div class="row">
                            <div class="col-md-12">
                                <div class="row row_next_action">
                                    <div class="col-6 d-inline form-inline">
                                        <input style="width: 87%" type="text" name="add_next_action" placeholder="Add New Next Action" class="form-control add_next_action_txt">
                                        <button class="btn btn-secondary add_next_action" style="position: absolute;  margin-left: 8px;">+</button>
                                    </div>
                                    <div class="col-6 d-inline form-inline">
                                        <div style="float: left; width: 88%">
                                            <select name="next_action" class="form-control next_action" data-id="{{:prop.id}}">
                                                <option value="">Select Next Action</option> 
                                                <?php foreach ($nextActionArr as $option) { ?>
                                                    <option {{if prop.customer_next_action_id == "<?php echo $option->id;?>"}} selected {{/if}}value="<?php echo $option->id;?>"><?php echo $option->name;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div style="float: right; width: 12%;">
                                            <a class="btn btn-image delete_next_action"><img src="/images/delete.png"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                       </div>
			        </td>
			        <td class="communication">
					   <div class="row">
					      <div class="col-md-12">
					         <div class="row">
					            <div class="col-md-12 form-inline">
					               <textarea rows="1" style="width: 90%" class="form-control quick-message-field" name="message" placeholder="Message"></textarea>
					               <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message" data-customerid="{{:prop.id}}"><img src="/images/filled-sent.png"></button>
					            </div>
					         </div>
					      </div>
					      <div class="col-md-12">
						   <div class="communication-div-{{:prop.id}}">
						      <div class="row">
						         <div class="col-md-12">
						            <span class="message-chat-txt" data-toggle="popover" data-placement="top" data-content="{{:prop.message}}" data-original-title="" title="">{{:prop.short_message}}</span>
						            <div class="col-md-12">
						            	<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="customer" data-limit="10" data-id="{{:prop.id}}" data-is_admin="1" data-is_hod_crm="" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
						            </div>
						         </div>
						      </div>
						   </div>
					   </div>
					</td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
