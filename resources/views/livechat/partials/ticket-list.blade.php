@foreach ($data as $key => $ticket)
          <tr>
               <td>{{ ++$i }}</td>
              <th>{{ $ticket->ticket_id }}
                    <!-- <div class="panel-group">
                         <div class="panel panel-default" style="width: 140px;">
                              <div class="panel-heading">
                                   <h4 class="panel-title">
                                        <a data-toggle="collapse" href="#collapse_{{$ticket->id}}">Messages</a>
                                   </h4>
                              </div>
                         </div>
                    </div> -->
              </th>

              <th>{{ $ticket->name}}</th>
              <th>{{ $ticket->email }}</th>
              <th>{{ $ticket->subject }}</th>
              <th>{{ $ticket->message }}</th>
              <th>{{ $ticket->assigned_to }}</th>
              <th>{{ $ticket->status_id }}</th>
              <th>
                    <div class="chat-righbox">
                         <a href="javascript:;" title="Email"  ><i class="fa fa-envelope" aria-hidden="true"></i></a>&nbsp;
                         <a href="javascript:;" title="Email"  ><i class="fa fa-whatsapp" aria-hidden="true"></i></a>&nbsp;
                         <a href="javascript:;" title="Email"  ><i class="fa fa-comments-o" aria-hidden="true"></i></a>&nbsp;
                         
                    </div>

          </tr>
               
@endforeach