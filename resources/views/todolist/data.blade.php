 @if($todolists->isEmpty())

            <tr>
                <td>
                    No Result Found
                </td>
            </tr>
        @else

          @foreach ($todolists as $todolist)
          
            <tr>
			        <td>{{ $todolist->id ?? '' }}</td>
             <td>{{ $todolist->title ?? '' }}</td>
              <td>{{ $todolist->username->name ?? '' }}</td>
              <td>{{ $todolist->status ?? '' }}</td>
              <td>{{ $todolist->todo_date ?? '' }}</td>
              <td>{{ $todolist->remark ?? '' }}</td>
              <td>
                <a class="btn btn-image" onclick="changetodolist({{ $todolist->id }})" ><img src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                <a onclick="getRemarkHistoryData({{ $todolist->id }})" class="btn" title="Remark history"><i class="fa fa-info-circle" aria-hidden="true"></i></a>
                {{-- <button onclick="sendtoWhatsapp({{ $todolist->id }})" class="btn btn-secondary btn-sm">Send to Whatsapp</button>< --}}
              </td>
            </tr>


          @endforeach

          @endif