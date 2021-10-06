
          @foreach ($emailAddress as $server)
            <tr>
               <td>
				<input type="checkbox" class="checkbox_ch" id="u{{ $server->id }}" name="userIds[]" value="{{ $server->id }}"></td>
              <td><!--td>
                  {{ $server->from_name }}
              </td>
              <td>
                  {{ $server->from_address }}
              </td-->
			 
                  {{ $server->username }}
              </td>
              <td>
                  {{ $server->password }}
              </td>
              <td>
                  {{ $server->recovery_phone }}
              </td>
              <td>
                  {{ $server->recovery_email }}
              </td>
              <td>
                  {{ $server->driver }}
              </td>
              <td>
                  {{ $server->host }}
              </td>
              <td>
                  {{ $server->port }}
              </td>
              <td>
                  {{ $server->encryption }}
              </td>
              
			  <td>
                  @if($server->website){{ $server->website->title }} @endif
              </td>
              <td>@if($server->is_success == 1) {{ 'Success' }} @elseif(isset($server->is_success)) {{'Error'}} @else {{'-'}} @endif</td>
              <td>
                  <button type="button" class="btn btn-image edit-email-addresses d-inline"  data-toggle="modal" data-target="#emailAddressEditModal" data-email-addresses="{{ json_encode($server) }}"><img src="/images/edit.png" /></button>
                  
                  <button type="button" class="btn btn-image view-email-history d-inline" data-id="{{ $server->id }}"><img width="2px;" src="/images/view.png"/></button>

                  {!! Form::open(['method' => 'DELETE','route' => ['email-addresses.destroy', $server->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
                  {!! Form::close() !!}
                   <a href="javascript:;" data-id="{{ $server->from_address }}" class="show-related-accounts" title="Show Account"><i class="fa fa-eye" aria-hidden="true"></i>
</a>
                   
				   <a href="javascript:;" onclick="sendtoWhatsapp({{ $server->id }})" title="Send to Whatsapp"><i class="fa fa-send-o"></i></button></td>
            
					<div id="sendToWhatsapp{{$server->id}}" class="modal fade" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title">Send to Whatsapp</h4>
										<button type="button" class="close" data-dismiss="modal">&times;</button>
									</div>
									<form action="{{ route('email.password.sendwhatsapp') }}" method="POST">
									@csrf
									<div class="modal-body">
										<div class="form-group">
											<select class="form-control" name="user_id">
												@foreach($users as $user)
												<option class="form-control" value="{{ $user->id }}">{{ $user->name }}</option>
												@endforeach
											</select>
											 <input type="hidden" name="id" value="{{ $server->id }}"/>
											<input type="hidden" name="send_message" value="1">
											<input type="hidden" name="send_on_whatsapp" value="1">
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-secondary">Update</button>
									</div>
									</form>
							</div>

						</div>
					</div>
			  </td>
            </tr>
			
			
	
          @endforeach
      